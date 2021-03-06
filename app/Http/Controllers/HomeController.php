<?php

namespace App\Http\Controllers;

use App;
use App\Repositories\BookRepository;
use App\Repositories\ShelfRepository;
use App\Repositories\UserRepository;
use App\Topic;
use Illuminate\Http\Request;
use Storage;
use App\Jobs\UpdateShelfCover;

class HomeController extends Controller
{
    protected $shelves;
    protected $users;
    protected $books;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ShelfRepository $shelves, UserRepository $users, BookRepository $books)
    {
        $this->middleware('auth', ['except' => [
            'index',
            'faq',
            'blog',
            'search',
            'bookshelves',
            'newsletter'
        ]]);
        $this->shelves = $shelves;
        $this->users = $users;
        $this->books = $books;
    }

    public function index()
    {
        $topics = Topic::with('followers')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->take(12);
        $shelves = $this->shelves->ourPicks();

        $favoriteBooks = $this->books->getFavorites();

        return view('home', [
            'shelves' => $shelves->toArray(),
            'topics' => json_encode($topics),
            'books' => json_encode($favoriteBooks),
        ]);
    }

    public function faq()
    {
        return view('static.about');
    }

    public function blog()
    {
        return view('static.story', [
            'title' => 'Follow my story of growing Booknshelf into a profitable online business.',
            'description' => "I'm sharing all my steps, revenue numbers, users count
                    and more. Make sure to get updates by subscribing to my mailing list",
            'ogImage' => 'https://booknshelf.com/img/backgrounds/hector-arguello-canals-142468.jpg'
        ]);
    }

    public function welcome(Request $request)
    {
        $user = $request->user();
        // No need to continue if the user has been on-boarded already
        if ($user->is_onboarded) {
            return redirect()->back();
        }
        return view('welcome', ['user' => $request->user()]);
    }

    public function landing()
    {
        return view('landing');
    }

    public function bookshelves()
    {
        $shelves = $this->shelves->ourPicks();
        return view('bookshelves', [
            'shelves' => $shelves,
        ]);
    }

    public function newsletter()
    {
        return view('newsletter', [
            'title' => "Join Booknshelf's Weekly Newsletter",
            'description' => "I'm sending book recommendations and summaries. 
                Free books and all sorts of book deals.
            I also love sharing my learnings from non-fiction books I read.",
            'ogImage' => 'https://booknshelf.com/img/backgrounds/aga-putra-125108.jpg'
        ]);
    }
}
