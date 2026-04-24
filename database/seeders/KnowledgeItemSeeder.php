<?php

namespace Database\Seeders;

use App\Models\KnowledgeItem;
use Illuminate\Database\Seeder;

class KnowledgeItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'question' => 'Alor Disha কী ধরনের প্রতিষ্ঠান?',
                'answer' => 'আলোর দিশা সুন্দরবনের দ্বীপাঞ্চলে সাংস্কৃতিক প্রশিক্ষণ, আধুনিক শিক্ষার জন্য কম্পিউটার প্রশিক্ষণ, সামাজিক ও সেবামূলক কাজ এবং বন্যা পরিস্থিতিতে ত্রাণ বিতরণসহ বিভিন্ন কাজে ব্রতী একটি প্রতিষ্ঠান।',
                'keywords' => 'alor disha কি,about,about alor disha,প্রতিষ্ঠান,কেমন প্রতিষ্ঠান,mission,উদ্দেশ্য',
                'priority' => 110,
                'is_active' => true,
            ],
            [
                'question' => 'Alor Disha full introduction',
                'answer' => 'Alor Disha is an institution working across Sundarban island regions for cultural training (recitation, yoga, drawing, dance, music, percussion), modern computer education, social service activities, and relief distribution during flood situations.',
                'keywords' => 'full intro,introduction,organization profile,overview,what does alor disha do',
                'priority' => 105,
                'is_active' => true,
            ],
            [
                'question' => 'Alor Disha special features কি?',
                'answer' => "বিশেষত্ব:\n1) ন্যূনতম খরচে প্রশিক্ষিত শিক্ষক-শিক্ষিকা দ্বারা প্রশিক্ষণ।\n2) মাসিক ৯৯ টাকায় কম্পিউটার প্রশিক্ষণ।\n3) জেলা ও রাজ্যস্তরের প্রতিযোগিতায় ছাত্রছাত্রীদের অংশগ্রহণের সুযোগ।\n4) অফলাইন ও অনলাইন ক্লাসের সুব্যবস্থা।",
                'keywords' => 'speciality,features,বিশেষত্ব,কেন আলোর দিশা,advantages,benefits',
                'priority' => 110,
                'is_active' => true,
            ],
            [
                'question' => 'What are the special features of Alor Disha?',
                'answer' => "Special Features:\n1) Training by qualified teachers at minimal cost.\n2) Computer training for everyone at only ₹99 per month.\n3) Student participation in district and state level competitions.\n4) Well-organized offline and online classes.",
                'keywords' => 'special features,advantages,why alor disha,low cost training,99 per month',
                'priority' => 111,
                'is_active' => true,
            ],
            [
                'question' => 'কম্পিউটার কোর্সের মাসিক ফি কত?',
                'answer' => 'কম্পিউটার প্রশিক্ষণের জন্য মাসিক ফি: মাত্র ৯৯ টাকা (সবার জন্য)।',
                'keywords' => '99,৯৯,monthly fee,computer fee,কম্পিউটার ফি,মাসিক ফি,টাকা',
                'priority' => 120,
                'is_active' => true,
            ],
            [
                'question' => 'Alor Disha কোথায় অবস্থিত?',
                'answer' => 'ঠিকানা: Sridharnagar, Patharpratima, South 24 Pgs, West Bengal - 743371।',
                'keywords' => 'address,location,alor disha address,where,ঠিকানা,কোথায়,patharpratima,sridharnagar,west bengal',
                'priority' => 100,
                'is_active' => true,
            ],
            [
                'question' => 'Alor Disha director কে?',
                'answer' => 'আলোর দিশার কর্ণধার/ডিরেক্টর: Satyaki Pahari।',
                'keywords' => 'director,founder,karnodhar,কর্ণধার,ডিরেক্টর,স্যাত্যকি,সাত্যকি পাহাড়ি,satyaki pahari',
                'priority' => 115,
                'is_active' => true,
            ],
            [
                'question' => 'Who is the director of Alor Disha?',
                'answer' => 'The director/lead of Alor Disha is Satyaki Pahari.',
                'keywords' => 'director of alor disha,who is satyaki pahari,lead of alor disha',
                'priority' => 116,
                'is_active' => true,
            ],
            [
                'question' => 'Alor Disha contact number কত?',
                'answer' => 'Call: +91 74079 17787 | WhatsApp: +91 74079 17787 | Email: satyakimv@gmail.com',
                'keywords' => 'phone,contact,number,whatsapp,call,কল,যোগাযোগ,নম্বর,email,mail',
                'priority' => 100,
                'is_active' => true,
            ],
            [
                'question' => 'Alor Disha email address কী?',
                'answer' => 'Official যোগাযোগের ইমেইল: satyakimv@gmail.com',
                'keywords' => 'email,mail,email address,ইমেইল,মেইল',
                'priority' => 110,
                'is_active' => true,
            ],
            [
                'question' => 'Alor Disha কবে প্রতিষ্ঠিত?',
                'answer' => 'আলোর দিশা প্রতিষ্ঠিত হয়েছে ২০১৮ সালে।',
                'keywords' => 'estd,established,founded,প্রতিষ্ঠা,কবে শুরু,2018,২০১৮',
                'priority' => 108,
                'is_active' => true,
            ],
            [
                'question' => 'When was Alor Disha established?',
                'answer' => 'Alor Disha was established in 2018.',
                'keywords' => 'established year,founded year,estd 2018,start year',
                'priority' => 109,
                'is_active' => true,
            ],
            [
                'question' => 'Alor Disha তে কতজন শিক্ষক আছেন?',
                'answer' => 'বর্তমানে ১২+ জন সক্রিয় শিক্ষক-শিক্ষিকা যুক্ত আছেন।',
                'keywords' => 'teacher count,শিক্ষক কতজন,faculty count,12+,১২+',
                'priority' => 105,
                'is_active' => true,
            ],
            [
                'question' => 'How many active teachers are there in Alor Disha?',
                'answer' => 'Currently, Alor Disha has 12+ active teachers.',
                'keywords' => 'active teachers,teacher count,faculty strength,12 plus teachers',
                'priority' => 106,
                'is_active' => true,
            ],
            [
                'question' => 'Alor Disha তে কতজন ছাত্রছাত্রী আছে?',
                'answer' => 'বর্তমানে ৬০০+ জন সক্রিয় ছাত্রছাত্রী রয়েছে।',
                'keywords' => 'student count,students,ছাত্রছাত্রী কতজন,600+,৬০০+',
                'priority' => 105,
                'is_active' => true,
            ],
            [
                'question' => 'How many active students are there in Alor Disha?',
                'answer' => 'Currently, Alor Disha has 600+ active students.',
                'keywords' => 'active students,student count,600 plus students,total learners',
                'priority' => 106,
                'is_active' => true,
            ],
            [
                'question' => 'Alor Disha কোন কোন department আছে?',
                'answer' => 'Departments: Computer, Yoga, Music, Dance, Fine Art, Recitation, Tabla, Sreekhol।',
                'keywords' => 'department,course,subject,program,কি কি কোর্স,departments,আবৃত্তি,যোগাসন,অঙ্কন,নৃত্য,সঙ্গীত,তালবাদ্য',
                'priority' => 112,
                'is_active' => true,
            ],
            [
                'question' => 'আলোর দিশায় কী কী সাংস্কৃতিক প্রশিক্ষণ হয়?',
                'answer' => 'সাংস্কৃতিক প্রশিক্ষণের মধ্যে রয়েছে: আবৃত্তি, যোগাসন, অঙ্কন, নৃত্য, সঙ্গীত এবং তালবাদ্য প্রশিক্ষণ।',
                'keywords' => 'সাংস্কৃতিক,আবৃত্তি,যোগাসন,অঙ্কন,নৃত্য,সঙ্গীত,তালবাদ্য,cultural training',
                'priority' => 112,
                'is_active' => true,
            ],
            [
                'question' => 'আলোর দিশায় অনলাইন ও অফলাইন ক্লাস আছে?',
                'answer' => 'হ্যাঁ, আলোর দিশায় অফলাইন এবং অনলাইন—দুই ধরনের ক্লাসের সুব্যবস্থা রয়েছে।',
                'keywords' => 'online,offline,class mode,অনলাইন,অফলাইন,ক্লাস',
                'priority' => 109,
                'is_active' => true,
            ],
            [
                'question' => 'প্রতিযোগিতায় অংশগ্রহণের সুযোগ আছে?',
                'answer' => 'হ্যাঁ। আলোর দিশা জেলা ও রাজ্য স্তরের বিভিন্ন প্রতিযোগিতায় ছাত্রছাত্রীদের অংশগ্রহণ করায়।',
                'keywords' => 'competition,জেলা স্তর,রাজ্য স্তর,contest,প্রতিযোগিতা',
                'priority' => 104,
                'is_active' => true,
            ],
            [
                'question' => 'ভর্তি সম্পর্কে জানতে চাই',
                'answer' => 'ভর্তি, ব্যাচ টাইমিং এবং ফি সংক্রান্ত আপডেটের জন্য +91 74079 17787 নম্বরে কল/WhatsApp করুন।',
                'keywords' => 'admission,enroll,ভর্তি,ফি,timing,batch',
                'priority' => 95,
                'is_active' => true,
            ],
            [
                'question' => 'Alor Disha tagline কী?',
                'answer' => 'Tagline:\nবাংলা: জ্ঞান ব্যতীত (বা জ্ঞান ছাড়া) শত জন্মেও মুক্তি সম্ভব নয়।\nEnglish: Without knowledge, liberation is not attained, even in a hundred births.\nSanskrit: ज्ञानविहीनस्य मुक्तिर्न भवति जन्मशतेनापि।',
                'keywords' => 'tagline,motto,slogan,জ্ঞান ব্যতীত,knowledge liberation,ज्ञानविहीनस्य',
                'priority' => 118,
                'is_active' => true,
            ],
            [
                'question' => 'গ্রামের শিক্ষকরা কি Alor Disha-র সাথে যুক্ত হতে পারবেন?',
                'answer' => 'হ্যাঁ, স্থানীয়/গ্রামের শিক্ষকরা আলোর দিশার সাথে যুক্ত হতে পারবেন। বিশেষ করে যারা পড়ান কিন্তু নিজস্ব প্রতিষ্ঠান না থাকায় সার্টিফিকেট পরীক্ষায় যুক্ত হতে পারেন না, তারা আলোর দিশার সাথে যুক্ত হয়ে certificate-exam ecosystem এর অংশ হতে পারবেন।',
                'keywords' => 'teacher join,connect teacher,certificate exam,সার্টিফিকেট পরীক্ষা,গ্রামের শিক্ষক,যুক্ত হওয়া,partner teacher',
                'priority' => 114,
                'is_active' => true,
            ],
            [
                'question' => 'Can independent teachers join Alor Disha for certificate exams?',
                'answer' => 'Yes. Independent/local teachers can connect with Alor Disha. This helps teachers who teach in villages but are not attached to a formal institution, so they can support students in certificate exam pathways.',
                'keywords' => 'teacher collaboration,join alor disha,certificate exam support,independent teacher,village teacher',
                'priority' => 115,
                'is_active' => true,
            ],
            [
                'question' => 'Does Alor Disha do social service and relief work?',
                'answer' => 'Yes. Alor Disha is actively involved in social and service-oriented activities, including relief distribution during flood situations.',
                'keywords' => 'social service,relief,flood,ত্রাণ,বন্যা,সেবামূলক',
                'priority' => 103,
                'is_active' => true,
            ],
            [
                'question' => 'quick contact alor disha',
                'answer' => 'Quick Contact: +91 74079 17787 (Call/WhatsApp) | Email: satyakimv@gmail.com | Address: Sridharnagar, Patharpratima, South 24 Pgs, West Bengal - 743371',
                'keywords' => 'quick contact,helpdesk,phone whatsapp email address',
                'priority' => 116,
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            KnowledgeItem::updateOrCreate(
                ['question' => $item['question']],
                $item
            );
        }
    }
}
