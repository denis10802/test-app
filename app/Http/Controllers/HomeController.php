<?php

namespace App\Http\Controllers;

use App\Components\DataTransferObjects\OfferDto;
use App\Http\Requests\OfferCreateFormRequest;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    public function __construct(private ResponseFactory $responseFactory)
    {
    }

    public function index(): Response
    {
        return $this->responseFactory->view('test-laravel-app.index');
    }

    public function create(OfferCreateFormRequest $formRequest): OfferDto
    {
        return $formRequest->toDto(
            $formRequest->post('title'),
            $formRequest->post('price'),
            $formRequest->post('description'),
            $formRequest->post('isActive'),
            $formRequest->post('publishAt'),
        );
    }
}
