<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Branch;
use App\Models\Student;
use App\Models\Subject;
use App\Models\FeeSetting;
use App\Models\FeePayment;
use App\Models\FeeTemplate;
use App\Models\FeeReceiptSetting;

class FeeTrackerController extends Controller
{
    private function getYearLabels()
    {
        return [
            'PP-1',
            'PP-2',
            'PR-1',
            '1ST',
            '2ND',
            '3RD',
            '4TH',
            '5TH',
            '6TH',
            '7TH',
            'KISHALAY-1',
            'KISHALAY-2',
            'SAHAJ PATH-1',
            'SAHAJ PATH-2',
        ];
    }

    private function getMonthNamesBn()
    {
        return [
            1 => 'জানুয়ারি',
            2 => 'ফেব্রুয়ারি',
            3 => 'মার্চ',
            4 => 'এপ্রিল',
            5 => 'মে',
            6 => 'জুন',
            7 => 'জুলাই',
            8 => 'আগস্ট',
            9 => 'সেপ্টেম্বর',
            10 => 'অক্টোবর',
            11 => 'নভেম্বর',
            12 => 'ডিসেম্বর',
        ];
    }
    private function getMonthNamesEn()
    {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];
    }
    private function resolveYearLabel(Student $student)
    {
        if (!empty($student->class_level)) {
            return trim($student->class_level);
        }

        $subjectYears = (array)($student->subject_years ?? []);
        if (!empty($subjectYears)) {
            $first = $subjectYears[0] ?? [];
            return $first['year_label'] ?? null;
        }

        return null;
    }

    private function renderTemplate($template, array $data)
    {
        // Convert specific values to uppercase for better visibility
        $uppercaseKeys = ['STUDENT_NAME', 'MONTH', 'MONTHS', 'BRANCH'];
        
        foreach ($data as $key => $value) {
            // Convert to uppercase if it's one of the specified keys
            if (in_array($key, $uppercaseKeys)) {
                $value = mb_strtoupper($value, 'UTF-8');
            }
            $template = str_replace($key, $value, $template);
        }
        return $template;
    }

    private function formatWhatsappNumber($number)
    {
        $clean = preg_replace('/\D+/', '', (string)$number);
        if ($clean === '') {
            return '';
        }

        if (str_starts_with($clean, '0')) {
            return '91' . substr($clean, 1);
        }

        if (str_starts_with($clean, '91')) {
            return $clean;
        }

        return '91' . $clean;
    }

    private function getDefaultTemplates()
    {
        return [
            'paid' => "প্রিয় STUDENT_NAME,\n\nআপনার MONTH মাসের বেতন (₹AMOUNT) সফলভাবে জমা হয়েছে।\n\n[রিসিট আইডি] RECEIPT_NO\n[বিভাগ] BRANCH\n\nএখন থেকে আপনি চাইলে অনলাইনেও (PhonePe/GPay) পেমেন্ট করতে পারেন।\n\nUPI ID: satyakimv-1@okicici\nLink: https://alordisha.mooo.com/pay\n\nআলোর দিশা (SSSP)-এর সাথে যুক্ত থাকার জন্য আপনাকে অসংখ্য ধন্যবাদ। আপনার উজ্জ্বল ভবিষ্যৎ কামনা করি।\n\n- আলোর দিশা",
            'due' => "প্রিয় STUDENT_NAME,\n\nআপনার MONTHS মাসের বকেয়া রয়েছে। মোট বকেয়া ₹AMOUNT টাকা।\n\n[বিভাগ] BRANCH\n\nঅনুগ্রহ করে যত তাড়াতাড়ি সম্ভব পরিশোধ করুন।\n\nUPI ID: satyakimv-1@okicici\nLink: https://alordisha.mooo.com/pay\n\nধন্যবাদ।\n\n- আলোর দিশা",
        ];
    }

    private function getDueMonthsList(Student $student, array $paidMonths, $year, $month)
    {
        $startYear = (int)$student->created_at->format('Y');
        $startMonth = (int)$student->created_at->format('n');
        $endYear = (int)$year;
        $endMonth = (int)$month;

        $monthNames = $this->getMonthNamesBn();
        $due = [];

        if ($endYear < $startYear) {
            return [];
        }

        if ($startYear < $endYear) {
            $startMonth = 1;
        }

        if ($startYear > $endYear) {
            return [];
        }

        for ($m = $startMonth; $m <= $endMonth; $m++) {
            if (!in_array($m, $paidMonths, true)) {
                $due[] = $monthNames[$m] ?? (string)$m;
            }
        }

        return $due;
    }

    public function index(Request $request)
    {
        $user = app('currentUser');
        $branchIds = (array)($user->branches ?? []);

        $branches = Branch::whereIn('id', $branchIds)->get();
        $selectedBranchId = (int)($request->get('branch_id') ?? ($branches->first()->id ?? 0));

        $month = (int)($request->get('month') ?? now()->format('n'));
        $year = (int)($request->get('year') ?? now()->format('Y'));

        $students = Student::where('teacher_id', $user->id)
            ->where('status', 'active')
            ->when($selectedBranchId, function ($query) use ($selectedBranchId) {
                return $query->where('branch_id', $selectedBranchId);
            })
            ->orderBy('name')
            ->get();

        $studentIds = $students->pluck('id')->all();

        // Extract all subject IDs from student subject_years
        $subjectIds = [];
        foreach ($students as $student) {
            $subjectYears = (array)($student->subject_years ?? []);
            foreach ($subjectYears as $sy) {
                $id = $sy['subject_id'] ?? null;
                if ($id) {
                    $subjectIds[] = $id;
                }
            }
        }
        $subjectIds = array_unique($subjectIds);

        // GET ALL SUBJECTS TO MAP IDS TO NAMES
        $subjects = Subject::whereIn('id', $subjectIds)->get()->keyBy('id');

        $feeSettings = FeeSetting::where('teacher_id', $user->id)
            ->when($selectedBranchId, function ($query) use ($selectedBranchId) {
                return $query->where('branch_id', $selectedBranchId);
            })
            ->get()
            ->keyBy('year_label');

        $payments = FeePayment::where('teacher_id', $user->id)
            ->when($selectedBranchId, function ($query) use ($selectedBranchId) {
                return $query->where('branch_id', $selectedBranchId);
            })
            ->where('month', $month)
            ->where('year', $year)
            ->get()
            ->keyBy(function ($payment) {
                return $payment->student_id . '_' . $payment->year_label;
            });

        $paymentHistory = FeePayment::where('teacher_id', $user->id)
            ->whereIn('student_id', $studentIds)
            ->where('year', $year)
            ->get()
            ->groupBy('student_id');

        $templates = FeeTemplate::where('teacher_id', $user->id)
            ->get()
            ->keyBy('type');

        $defaults = $this->getDefaultTemplates();
        $paidTemplate = $templates['paid']->template_text ?? $defaults['paid'];
        $dueTemplate = $templates['due']->template_text ?? $defaults['due'];

        $monthNames = $this->getMonthNamesEn();

        $rows = [];
        foreach ($students as $student) {
            $subjectYears = (array)($student->subject_years ?? []);
            
            // If student has no subject_years, create a single row with class_level as year_label
            if (empty($subjectYears)) {
                $yearLabel = $student->class_level ?? null;
                $amount = $yearLabel && isset($feeSettings[$yearLabel])
                    ? (int)$feeSettings[$yearLabel]->amount
                    : null;

                $payment = $payments[$student->id . '_' . ($yearLabel ?? '')] ?? null;
                $isPaid = $payment !== null;

                $paidMonths = $paymentHistory[$student->id] ?? collect();
                $paidMonthsList = $paidMonths->pluck('month')->map(function ($m) {
                    return (int)$m;
                })->all();

                $dueMonths = $this->getDueMonthsList($student, $paidMonthsList, $year, $month);
                $dueCount = count($dueMonths);

                $amountTotal = $amount ? $amount * max($dueCount, 1) : 0;

                $messageData = [
                    'STUDENT_NAME' => $student->name,
                    'UNIT' => $yearLabel ?? 'N/A',
                    'MONTH' => $monthNames[$month] ?? (string)$month,
                    'MONTHS' => $dueCount > 0 ? implode(', ', $dueMonths) : ($monthNames[$month] ?? (string)$month),
                    'YEAR' => (string)$year,
                    'AMOUNT' => $amount ? number_format($amountTotal) : '0',
                    'DATE' => now()->format('d-m-Y'),
                    'BRANCH' => $student->branch?->name ?? 'N/A',
                    'RECEIPT_NO' => $payment?->receipt_no ?? 'N/A',
                ];

                $paidMessage = $this->renderTemplate($paidTemplate, $messageData);
                $dueMessage = $this->renderTemplate($dueTemplate, $messageData);

                $whatsapp = $this->formatWhatsappNumber($student->whatsapp ?? $student->phone ?? '');

                $rows[] = [
                    'student' => $student,
                    'subject_name' => 'N/A',
                    'year_label' => $yearLabel,
                    'amount' => $amount,
                    'paid' => $isPaid,
                    'payment' => $payment,
                    'paid_message' => $paidMessage,
                    'due_message' => $dueMessage,
                    'whatsapp' => $whatsapp,
                    'due_months' => $dueMonths,
                ];
            } else {
                // Create a row for each subject the student is enrolled in
                foreach ($subjectYears as $sy) {
                    $subjectId = $sy['subject_id'] ?? null;
                    $yearLabel = $sy['year_label'] ?? null;
                    
                    if (!$subjectId || !$yearLabel) {
                        continue;
                    }

                    $subjectName = $subjects[$subjectId]?->name ?? 'Unknown';
                    $amount = isset($feeSettings[$yearLabel])
                        ? (int)$feeSettings[$yearLabel]->amount
                        : null;

                    $paymentKey = $student->id . '_' . $yearLabel;
                    $payment = $payments[$paymentKey] ?? null;
                    $isPaid = $payment !== null;

                    $paidMonths = $paymentHistory[$student->id] ?? collect();
                    $paidMonthsList = $paidMonths->pluck('month')->map(function ($m) {
                        return (int)$m;
                    })->all();

                    $dueMonths = $this->getDueMonthsList($student, $paidMonthsList, $year, $month);
                    $dueCount = count($dueMonths);

                    $amountTotal = $amount ? $amount * max($dueCount, 1) : 0;

                    $messageData = [
                        'STUDENT_NAME' => $student->name,
                        'UNIT' => $subjectName . ' - ' . $yearLabel,
                        'MONTH' => $monthNames[$month] ?? (string)$month,
                        'MONTHS' => $dueCount > 0 ? implode(', ', $dueMonths) : ($monthNames[$month] ?? (string)$month),
                        'YEAR' => (string)$year,
                        'AMOUNT' => $amount ? number_format($amountTotal) : '0',
                        'DATE' => now()->format('d-m-Y'),
                        'BRANCH' => $student->branch?->name ?? 'N/A',
                        'RECEIPT_NO' => $payment?->receipt_no ?? 'N/A',
                    ];

                    $paidMessage = $this->renderTemplate($paidTemplate, $messageData);
                    $dueMessage = $this->renderTemplate($dueTemplate, $messageData);

                    $whatsapp = $this->formatWhatsappNumber($student->whatsapp ?? $student->phone ?? '');

                    $rows[] = [
                        'student' => $student,
                        'subject_name' => $subjectName,
                        'year_label' => $yearLabel,
                        'amount' => $amount,
                        'paid' => $isPaid,
                        'payment' => $payment,
                        'paid_message' => $paidMessage,
                        'due_message' => $dueMessage,
                        'whatsapp' => $whatsapp,
                        'due_months' => $dueMonths,
                    ];
                }
            }
        }

        // MERGE DUPLICATE STUDENTS (same student_id + branch_id)
        $mergedRows = [];
        $groupedRows = [];
        
        foreach ($rows as $row) {
            $key = $row['student']->id . '_' . $row['student']->branch_id;
            if (!isset($groupedRows[$key])) {
                $groupedRows[$key] = [];
            }
            $groupedRows[$key][] = $row;
        }

        foreach ($groupedRows as $key => $group) {
            if (count($group) === 1) {
                // Single row - no merge needed
                $mergedRows[] = $group[0];
            } else {
                // Multiple rows for same student - merge subjects
                $firstRow = $group[0];
                $subjects = [];
                $totalAmount = 0;
                $hasUnpaid = false;

                foreach ($group as $row) {
                    $subjects[] = [
                        'name' => $row['subject_name'],
                        'year_label' => $row['year_label'],
                        'amount' => $row['amount'],
                        'paid' => $row['paid'],
                        'payment' => $row['payment'],
                    ];
                    $totalAmount += $row['amount'] ?? 0;
                    if (!$row['paid']) {
                        $hasUnpaid = true;
                    }
                }

                // Combine subjects for display (e.g., "Yoga - PP-1, Recitation - 1ST")
                $subjectDisplay = implode(', ', array_map(function($s) {
                    return $s['name'] . ' - ' . $s['year_label'];
                }, $subjects));

                $mergedRows[] = [
                    'student' => $firstRow['student'],
                    'subject_name' => $subjectDisplay,
                    'subjects' => $subjects,
                    'year_label' => null, // No single year_label for merged
                    'amount' => $totalAmount,
                    'paid' => !$hasUnpaid,
                    'payment' => $hasUnpaid ? null : $group[0]['payment'],
                    'paid_message' => $firstRow['paid_message'],
                    'due_message' => $firstRow['due_message'],
                    'whatsapp' => $firstRow['whatsapp'],
                    'due_months' => $firstRow['due_months'],
                    'is_merged' => true, // Flag to indicate merged row
                ];
            }
        }

        return view('teacher.fees.index', [
            'branches' => $branches,
            'rows' => $mergedRows,
            'selectedBranchId' => $selectedBranchId,
            'month' => $month,
            'year' => $year,
            'monthNames' => $monthNames,
        ]);
    }

    public function markPaid(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'year_label' => 'required|string',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
        ]);

        $user = app('currentUser');
        $student = Student::where('teacher_id', $user->id)->findOrFail($request->student_id);

        $feeSetting = FeeSetting::where('teacher_id', $user->id)
            ->where('branch_id', $request->branch_id)
            ->where('year_label', $request->year_label)
            ->first();

        if (!$feeSetting) {
            return back()->withErrors(['amount' => 'এই ইউনিটের ফি সেট করা হয়নি।']);
        }

        $payment = FeePayment::updateOrCreate(
            [
                'teacher_id' => $user->id,
                'student_id' => $student->id,
                'month' => (int)$request->month,
                'year' => (int)$request->year,
            ],
            [
                'branch_id' => (int)$request->branch_id,
                'year_label' => $request->year_label,
                'amount' => (int)$feeSetting->amount,
                'paid_at' => now(),
            ]
        );

        if (!$payment->receipt_no) {
            $payment->receipt_no = 'AD-' . $payment->id . '-' . now()->format('Ym');
            $payment->save();
        }

        return back()->with('success', 'Payment saved successfully.');
    }

    public function markPaidMultiple(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'year_labels' => 'required|array',
            'year_labels.*' => 'required|string',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
        ]);

        $user = app('currentUser');
        $student = Student::where('teacher_id', $user->id)->findOrFail($request->student_id);

        $totalAmount = 0;
        $yearLabelsCombined = [];

        foreach ($request->year_labels as $yearLabel) {
            $feeSetting = FeeSetting::where('teacher_id', $user->id)
                ->where('branch_id', $request->branch_id)
                ->where('year_label', $yearLabel)
                ->first();

            if ($feeSetting) {
                $totalAmount += (int)$feeSetting->amount;
                $yearLabelsCombined[] = $yearLabel;

                // Create individual payment records for each subject
                FeePayment::updateOrCreate(
                    [
                        'teacher_id' => $user->id,
                        'student_id' => $student->id,
                        'month' => (int)$request->month,
                        'year' => (int)$request->year,
                        'year_label' => $yearLabel,
                    ],
                    [
                        'branch_id' => (int)$request->branch_id,
                        'amount' => (int)$feeSetting->amount,
                        'paid_at' => now(),
                    ]
                );
            }
        }

        // Find the first payment to set receipt number if needed
        $payment = FeePayment::where('teacher_id', $user->id)
            ->where('student_id', $student->id)
            ->where('month', (int)$request->month)
            ->where('year', (int)$request->year)
            ->whereIn('year_label', $yearLabelsCombined)
            ->first();

        if ($payment && !$payment->receipt_no) {
            $payment->receipt_no = 'AD-' . $payment->id . '-' . now()->format('Ym');
            $payment->save();
        }

        return back()->with('success', 'All subjects marked as paid successfully.');
    }

    public function settings(Request $request)
    {
        $user = app('currentUser');
        $branchIds = (array)($user->branches ?? []);

        $branches = Branch::whereIn('id', $branchIds)->get();
        $selectedBranchId = (int)($request->get('branch_id') ?? ($branches->first()->id ?? 0));

        $yearLabels = $this->getYearLabels();

        $feeSettings = FeeSetting::where('teacher_id', $user->id)
            ->when($selectedBranchId, function ($query) use ($selectedBranchId) {
                return $query->where('branch_id', $selectedBranchId);
            })
            ->get()
            ->keyBy('year_label');

        $templates = FeeTemplate::where('teacher_id', $user->id)
            ->get()
            ->keyBy('type');

        $defaults = $this->getDefaultTemplates();
        $paidTemplate = $templates['paid']->template_text ?? $defaults['paid'];
        $dueTemplate = $templates['due']->template_text ?? $defaults['due'];

        $receiptSettings = FeeReceiptSetting::firstOrCreate(
            ['teacher_id' => $user->id],
            ['header_text' => 'Alor Disha']
        );

        return view('teacher.fees.settings', [
            'branches' => $branches,
            'selectedBranchId' => $selectedBranchId,
            'yearLabels' => $yearLabels,
            'feeSettings' => $feeSettings,
            'paidTemplate' => $paidTemplate,
            'dueTemplate' => $dueTemplate,
            'receiptSettings' => $receiptSettings,
        ]);
    }

    public function saveFees(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|integer',
            'amounts' => 'required|array',
        ]);

        $user = app('currentUser');
        $branchId = (int)$request->branch_id;
        $yearLabels = $this->getYearLabels();

        DB::transaction(function () use ($user, $branchId, $request, $yearLabels) {
            foreach ($yearLabels as $label) {
                $amount = (int)($request->amounts[$label] ?? 0);
                if ($amount <= 0) {
                    continue;
                }

                FeeSetting::updateOrCreate(
                    [
                        'teacher_id' => $user->id,
                        'branch_id' => $branchId,
                        'year_label' => $label,
                    ],
                    ['amount' => $amount]
                );
            }
        });

        return back()->with('success', 'Fee settings saved successfully.');
    }

    public function saveTemplates(Request $request)
    {
        $request->validate([
            'paid_template' => 'required|string',
            'due_template' => 'required|string',
        ]);

        $user = app('currentUser');

        FeeTemplate::updateOrCreate(
            ['teacher_id' => $user->id, 'type' => 'paid'],
            ['template_text' => $request->paid_template]
        );

        FeeTemplate::updateOrCreate(
            ['teacher_id' => $user->id, 'type' => 'due'],
            ['template_text' => $request->due_template]
        );

        return back()->with('success', 'Message templates saved successfully.');
    }

    public function saveLogo(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|max:512',
            'header_text' => 'nullable|string',
        ]);

        $user = app('currentUser');
        $settings = FeeReceiptSetting::firstOrCreate(['teacher_id' => $user->id]);

        if ($request->hasFile('logo')) {
            if ($settings->logo_path) {
                Storage::disk('public')->delete($settings->logo_path);
            }

            $path = $request->file('logo')->store('fee_receipts', 'public');
            $settings->logo_path = $path;
        }

        if ($request->filled('header_text')) {
            $settings->header_text = $request->header_text;
        }

        $settings->save();

        return back()->with('success', 'Fee settings saved successfully.');
    }

    public function receipt(FeePayment $payment)
    {
        $user = app('currentUser');
        if ($payment->teacher_id !== $user->id) {
            abort(403);
        }

        $student = Student::findOrFail($payment->student_id);
        $branch = Branch::find($payment->branch_id);

        // Check if there are multiple payments for this student in the same month (merged subjects)
        $allPayments = FeePayment::where('teacher_id', $user->id)
            ->where('student_id', $payment->student_id)
            ->where('month', $payment->month)
            ->where('year', $payment->year)
            ->where('branch_id', $payment->branch_id)
            ->get();

        $totalAmount = $allPayments->sum('amount');
        $yearLabels = $allPayments->pluck('year_label')->filter()->toArray();
        $combinedYearLabel = count($yearLabels) > 1 ? implode(', ', $yearLabels) : $payment->year_label;

        $settings = FeeReceiptSetting::firstOrCreate(
            ['teacher_id' => $user->id],
            ['header_text' => 'Alor Disha']
        );

        return view('teacher.fees.receipt', [
            'payment' => $payment,
            'student' => $student,
            'branch' => $branch,
            'settings' => $settings,
            'monthName' => $this->getMonthNamesEn()[$payment->month] ?? (string)$payment->month,
            'totalAmount' => $totalAmount,
            'combinedYearLabel' => $combinedYearLabel,
            'isMerged' => count($yearLabels) > 1,
        ]);
    }

    private function buildReportData($user, $month, $year)
    {
        $branchIds = (array)($user->branches ?? []);
        $branches = Branch::whereIn('id', $branchIds)->get();

        $monthName = $this->getMonthNamesEn()[$month] ?? (string)$month;

        $summary = [];
        $totalPaid = 0;
        $totalDue = 0;

        foreach ($branches as $branch) {
            $students = Student::where('teacher_id', $user->id)
                ->where('status', 'active')
                ->where('branch_id', $branch->id)
                ->orderBy('name')
                ->get();

            // Extract all subject IDs from student subject_years
            $subjectIds = [];
            foreach ($students as $student) {
                $subjectYears = (array)($student->subject_years ?? []);
                foreach ($subjectYears as $sy) {
                    $id = $sy['subject_id'] ?? null;
                    if ($id) {
                        $subjectIds[] = $id;
                    }
                }
            }
            $subjectIds = array_unique($subjectIds);

            // GET ALL SUBJECTS TO MAP IDS TO NAMES
            $subjects = Subject::whereIn('id', $subjectIds)->get()->keyBy('id');

            $feeSettings = FeeSetting::where('teacher_id', $user->id)
                ->where('branch_id', $branch->id)
                ->get()
                ->keyBy('year_label');

            $payments = FeePayment::where('teacher_id', $user->id)
                ->where('branch_id', $branch->id)
                ->where('month', $month)
                ->where('year', $year)
                ->get()
                ->keyBy(function ($payment) {
                    return $payment->student_id . '_' . $payment->year_label;
                });

            $rows = [];
            $branchPaid = 0;
            $branchDue = 0;

            foreach ($students as $student) {
                $subjectYears = (array)($student->subject_years ?? []);
                
                // If student has no subject_years, create a single row with class_level
                if (empty($subjectYears)) {
                    $yearLabel = $student->class_level ?? null;
                    $amount = $yearLabel && isset($feeSettings[$yearLabel])
                        ? (int)$feeSettings[$yearLabel]->amount
                        : 0;

                    $paymentKey = $student->id . '_' . ($yearLabel ?? '');
                    $isPaid = isset($payments[$paymentKey]);
                    if ($isPaid) {
                        $branchPaid += $amount;
                    } else {
                        $branchDue += $amount;
                    }

                    $rows[] = [
                        'student' => $student,
                        'subject_name' => 'N/A',
                        'year_label' => $yearLabel,
                        'amount' => $amount,
                        'paid' => $isPaid,
                    ];
                } else {
                    // Create a row for each subject the student is enrolled in
                    foreach ($subjectYears as $sy) {
                        $subjectId = $sy['subject_id'] ?? null;
                        $yearLabel = $sy['year_label'] ?? null;
                        
                        if (!$subjectId || !$yearLabel) {
                            continue;
                        }

                        $subjectName = $subjects[$subjectId]?->name ?? 'Unknown';
                        $amount = isset($feeSettings[$yearLabel])
                            ? (int)$feeSettings[$yearLabel]->amount
                            : 0;

                        $paymentKey = $student->id . '_' . $yearLabel;
                        $isPaid = isset($payments[$paymentKey]);
                        if ($isPaid) {
                            $branchPaid += $amount;
                        } else {
                            $branchDue += $amount;
                        }

                        $rows[] = [
                            'student' => $student,
                            'subject_name' => $subjectName,
                            'year_label' => $yearLabel,
                            'amount' => $amount,
                            'paid' => $isPaid,
                        ];
                    }
                }
            }

            // MERGE DUPLICATE STUDENTS (same student_id + branch_id)
            $mergedRows = [];
            $groupedRows = [];
            
            foreach ($rows as $row) {
                $key = $row['student']->id . '_' . $row['student']->branch_id;
                if (!isset($groupedRows[$key])) {
                    $groupedRows[$key] = [];
                }
                $groupedRows[$key][] = $row;
            }

            foreach ($groupedRows as $key => $group) {
                if (count($group) === 1) {
                    $mergedRows[] = $group[0];
                } else {
                    $firstRow = $group[0];
                    $subjects = [];
                    $totalAmount = 0;
                    $hasUnpaid = false;

                    foreach ($group as $row) {
                        $subjects[] = [
                            'name' => $row['subject_name'],
                            'year_label' => $row['year_label'],
                            'amount' => $row['amount'],
                        ];
                        $totalAmount += $row['amount'] ?? 0;
                        if (!$row['paid']) {
                            $hasUnpaid = true;
                        }
                    }

                    $subjectDisplay = implode(', ', array_map(function($s) {
                        return $s['name'] . ' - ' . $s['year_label'];
                    }, $subjects));

                    $mergedRows[] = [
                        'student' => $firstRow['student'],
                        'subject_name' => $subjectDisplay,
                        'subjects' => $subjects,
                        'year_label' => null,
                        'amount' => $totalAmount,
                        'paid' => !$hasUnpaid,
                        'is_merged' => true, // Flag to indicate merged row
                    ];
                }
            }

            $totalPaid += $branchPaid;
            $totalDue += $branchDue;

            $summary[] = [
                'branch' => $branch,
                'rows' => $mergedRows,
                'paid_total' => $branchPaid,
                'due_total' => $branchDue,
            ];
        }

        return [
            'branches' => $branches,
            'month' => $month,
            'year' => $year,
            'monthName' => $monthName,
            'monthNames' => $this->getMonthNamesEn(),
            'summary' => $summary,
            'totalPaid' => $totalPaid,
            'totalDue' => $totalDue,
        ];
    }

    public function report(Request $request)
    {
        $user = app('currentUser');
        $month = (int)($request->get('month') ?? now()->format('n'));
        $year = (int)($request->get('year') ?? now()->format('Y'));
        $data = $this->buildReportData($user, $month, $year);

        return view('teacher.fees.report', $data);
    }

    public function reportPdf(Request $request)
    {
        if (!class_exists('Dompdf\\Dompdf')) {
            return $this->report($request)
                ->with('error', 'PDF তৈরি করতে dompdf ইনস্টল করুন।');
        }

        $user = app('currentUser');
        $month = (int)($request->get('month') ?? now()->format('n'));
        $year = (int)($request->get('year') ?? now()->format('Y'));

        $data = $this->buildReportData($user, $month, $year);
        $html = view('teacher.fees.report_pdf', $data)->render();

        $dompdf = new \Dompdf\Dompdf();
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();

        return $dompdf->stream('monthly-report.pdf');
    }
}
