<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionsController extends Controller
{
    public function index(Request $request)
    {
        // Fetch questions with user, book, and (optional) answer/admin
        $questions = DB::table('questions as q')
            ->join('users as u', 'q.user_id', '=', 'u.user_id')
            ->join('books as b', 'q.book_id', '=', 'b.book_id')
            ->leftJoin('answers as a', 'q.question_id', '=', 'a.question_id')
            ->leftJoin('admin as ad', 'a.admin_id', '=', 'ad.admin_id')
            ->orderBy('q.created_at', 'desc')
            ->select(
                'q.question_id', 'q.question_text', 'q.created_at',
                'u.username', 'u.user_id',
                'b.title as book_title', 'b.book_id',
                'a.answer_id', 'a.answer_text', 'a.created_at as answer_created',
                'ad.username as admin_name'
            )
            ->get();

        return view('admin.question', [
            'questions'       => $questions,
            'success_message' => session('success_message'),
            'error_message'   => session('error_message'),
        ]);
    }

    public function storeAnswer(Request $request)
    {
        $data = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,question_id'],
            'answer_text' => ['required', 'string'],
        ]);

        // Use the logged-in admin id from your auth middleware/session
        $adminId = auth()->user()->admin_id ?? session('admin_id'); // fallback to session if needed

        try {
            DB::table('answers')->insert([
                'question_id' => $data['question_id'],
                'answer_text' => $data['answer_text'],
                'admin_id'    => $adminId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            return redirect()->route('admin.question')->with('success_message', 'Answer submitted successfully!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.question')->with('error_message', 'Error submitting answer: '.$e->getMessage());
        }
    }

    public function updateAnswer(Request $request, $answer_id)
    {
        $data = $request->validate([
            'answer_text' => ['required', 'string'],
        ]);

        try {
            DB::table('answers')
                ->where('answer_id', $answer_id)
                ->update([
                    'answer_text' => $data['answer_text'],
                    'updated_at'  => now(),
                ]);

            return redirect()->route('admin.question')->with('success_message', 'Answer updated successfully!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.question')->with('error_message', 'Error updating answer: '.$e->getMessage());
        }
    }

    public function destroyAnswer($answer_id)
    {
        try {
            DB::table('answers')->where('answer_id', $answer_id)->delete();
            return redirect()->route('admin.question')->with('success_message', 'Answer deleted successfully!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.question')->with('error_message', 'Error deleting answer: '.$e->getMessage());
        }
    }
}
