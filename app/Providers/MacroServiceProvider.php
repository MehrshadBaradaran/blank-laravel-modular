<?php

namespace App\Providers;

use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Response;

class MacroServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //.................Builder.................
        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                    // Check if the attribute is not an expression and contains a dot (indicating a related model)
                        !($attribute instanceof Expression) && str_contains((string)$attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm) {
                            [$relation, $relatedAttribute] = explode('.', (string)$attribute);

                            $query->orWhereHas($relation, function (Builder $query) use ($relatedAttribute, $searchTerm) {
                                $query->where($relatedAttribute, 'LIKE', "%$searchTerm%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm) {
                            $query->orWhere($attribute, 'LIKE', "%$searchTerm%");
                        }
                    );
                }
            });
        });


        //.................Response.................
        Response::macro('list', function ($data) {
            return response()->json([
                'success' => true,
                'message' => null,
                'data' => $data['data'],
                'pagination_data' => [
                    'links' => $data['links'],
                    'meta' => $data['meta'],
                ],
            ]);
        });

        Response::macro('success', function (string $message = null, int $statusCode = 200, $data = null) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ], $statusCode);
        });

        Response::macro('error', function ($exception = null, string $message = null, int $statusCode = 500, $data = null) {
            if ($exception) {
                $logMessage = "\n message: {$exception->getMessage()}";
                $logMessage .= "\n in: {$exception->getFile()}";
                $logMessage .= "\n line: {$exception->getLine()}";
                $logMessage .= "\n request data: " . print_r(request()->all(), true);
//            $logMessage .="\n trace:\n{$exception->getTraceAsString()}";

                Log::channel('error')->error($logMessage);
            }

            return response()->json([
                'success' => false,
                'message' => $message,
                'data' => $data,
            ], $statusCode);
        });
    }
}
