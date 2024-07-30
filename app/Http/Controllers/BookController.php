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
        return response()->json([
            'success' => true,
            'message' => 'Book data successfully',
            'result' => $books
        ], 200);
    }

    public function bookInsert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => "Validation fails.",
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
            'category_id' => $request->input('category_id'),
            'price' => $request->input('price'),
            'image' => $filename,
        ]);

        return response()->json(['success' => true,'message' => 'Book added successfully!', 'result' => $book], 201);
    }

    public function bookUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:books,id',
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => "Validation fails.",
                'errors' => $validator->errors()
            ], 400);
        }

        $book = Book::find($request->input('id'));

        if ($book) {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '.' . $image->getClientOriginalExtension();
                $image->move('images', $filename);
                $book->image = $filename;
            }

            $book->update([
                'id' => $request->input('id'),
                'name' => $request->input('name'),
                'category_id' => $request->input('category_id'),
                'price' => $request->input('price'),
            ]);

            return response()->json(['success' => true, 'message' => 'Book updated successfully!','result' => $book,], 200);
        } else {
            return response()->json(['success' => false,'message' => 'Book not found'], 404);
        }
    }

    public function bookDestroy(Request $request)
    {
        $book = Book::find($request->input('id'));

        if ($book) {
            $book->delete();
            return response()->json(['success' => true,'message' => 'Book deleted successfully!','result' => $book], 200);
        } else {
            return response()->json(['success' => false,'message' => 'Book not found'], 404);
        }
    }
}
