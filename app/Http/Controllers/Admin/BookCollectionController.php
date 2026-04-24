<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookCollectionController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $authorFilter = trim((string) $request->input('author', ''));

        $collections = BookCollection::query()
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where(function ($subQuery) use ($like) {
                    $subQuery->where('book_name', 'like', $like)
                        ->orWhere('author', 'like', $like);
                });
            })
            ->when($authorFilter !== '', function ($query) use ($authorFilter) {
                $query->where('author', $authorFilter);
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $authors = BookCollection::query()
            ->select('author')
            ->whereNotNull('author')
            ->where('author', '!=', '')
            ->distinct()
            ->orderBy('author')
            ->pluck('author');

        return view('admin.collections.index', compact(
            'collections',
            'authors',
            'search',
            'authorFilter'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cover_image' => ['required', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
            'book_name' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
        ]);

        $imagePath = $request->file('cover_image')->store('book-collections', 'public');

        $collection = BookCollection::create([
            'cover_image_path' => $imagePath,
            'book_name' => $validated['book_name'],
            'author' => $validated['author'],
            'book_type' => 'Physical Book',
            'notes' => null,
            'added_by' => session('user_id'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Collection item added successfully.',
                'id' => $collection->id,
            ]);
        }

        return redirect()->route('admin.collections.index')->with('success', 'Collection item added successfully.');
    }

    public function update(Request $request, BookCollection $collection)
    {
        $validated = $request->validate([
            'cover_image' => ['nullable', 'image', 'max:5120', 'mimes:jpg,jpeg,png,webp'],
            'book_name' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
        ]);

        $updateData = [
            'book_name' => $validated['book_name'],
            'author' => $validated['author'],
            'book_type' => 'Physical Book',
            'notes' => null,
        ];

        if ($request->hasFile('cover_image')) {
            if ($collection->cover_image_path && Storage::disk('public')->exists($collection->cover_image_path)) {
                Storage::disk('public')->delete($collection->cover_image_path);
            }

            $updateData['cover_image_path'] = $request->file('cover_image')->store('book-collections', 'public');
        }

        $collection->update($updateData);

        return redirect()->route('admin.collections.index')->with('success', 'Collection item updated successfully.');
    }

    public function destroy(BookCollection $collection)
    {
        if ($collection->cover_image_path && Storage::disk('public')->exists($collection->cover_image_path)) {
            Storage::disk('public')->delete($collection->cover_image_path);
        }

        $collection->delete();

        return redirect()->route('admin.collections.index')->with('success', 'Collection item deleted successfully.');
    }
}
