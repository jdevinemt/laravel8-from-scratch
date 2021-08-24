<?php

namespace App\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class Post {

    public function __construct(public string $title, public string $excerpt, public int $date, public string $body, public string $slug){

    }


    /**
     * Get all posts
     *
     * @return Collection
     */
    public static function all(): Collection {

        return cache()->rememberForever('posts.all', function(){
            return collect(File::files(resource_path('posts')))
                ->map(fn($file) => YamlFrontMatter::parseFile($file))
                ->map(fn($document) => new Post(
                    $document->title,
                    $document->excerpt,
                    $document->date,
                    $document->body(),
                    $document->slug
                ))
                ->sortByDesc('date');
        });

    }


    /**
     * Get post from requested slug
     *
     * @param string $slug slug of post to find
     * @return null|Post
     */
    public static function find(string $slug): ?self {

        return static::all()->firstWhere('slug', '=', $slug);

    }


    /**
     * Get post from requested slug or fail
     *
     * @param string $slug slug of post to find
     * @return Post
     */
    public static function findOrFail(string $slug): self {

        $post = static::find($slug);

        if(!$post){
            throw new ModelNotFoundException();
        }

        return $post;

    }

}
