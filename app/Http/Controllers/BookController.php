<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    public function book()
    {
        $books = Book::all();
        return response()->json(['books' => $books], 200);
    }

    public function bookInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_name' => 'required',
            'price' => 'required|numeric',
            'image' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $filename = '';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '.' . $image->getClientOriginalExtension();
            $image->move('images', $filename);
        }

        $book = Book::create([
            'name' => $request->input('name'),
            'category_name' => $request->input('category_name'),
            'price' => $request->input('price'),
            'image' => $filename,
        ]);

        return response()->json(['message' => 'Book added successfully!', 'book' => $book], 201);
    }

    public function bookUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_name' => 'required',
            'price' => 'required|numeric',
            'image' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        $book = Book::find($id);

        if ($book) {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $image->move('images', $filename);
                $book->image = $filename;
            }

            $book->update([
                'name' => $request->input('name'),
                'category_name' => $request->input('category_name'),
                'price' => $request->input('price'),
            ]);

            return response()->json(['book' => $book, 'message' => 'Book updated successfully!'], 200);
        } else {
            return response()->json(['message' => 'Book not found'], 404);
        }
    }

    public function bookDestroy($id)
    {
        $book = Book::find($id);

        if ($book) {
            $book->delete();
            return response()->json(['message' => 'Book deleted successfully!'], 200);
        } else {
            return response()->json(['message' => 'Book not found'], 404);
        }
    }
}
