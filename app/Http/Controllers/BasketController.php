<?php

namespace App\Http\Controllers;

//Requests
use App\Http\Requests\AddDocumentBasketRequest;
use App\Http\Requests\CreateBasketRequest;

//Api resources
use App\Http\Resources\BasketApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

//Services
use App\Services\BasketService;

//Responses
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

//Others
use DateTime;

class BasketController extends Controller
{

    protected $basketService;

    public function __construct(BasketService $basketService)
    {
        $this->basketService = $basketService;
    }

    /**
     * Creates a basket using the given data from the request
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function create(CreateBasketRequest $request): JsonResponse
    {
        $requestData = $request->validated();
        $expectedDueDate = DateTime::createFromFormat('Y-m-d', $requestData['expected_due_date']);
        $this->basketService->createBasket($requestData['project_id'], $requestData['customer_id'], $requestData['target_languages'], $expectedDueDate);

        return response()->json([
            'created' => true
        ]);
    }

    /**
     * Adds a document to the given basket
     *
     * @param AddDocumentBasketRequest $request
     * @return Response
     */
    public function addDocument(AddDocumentBasketRequest $request): JsonResponse
    {
        $requestData = $request->validated();

        $this->basketService->addDocument(
            $requestData['project_id'],
            $requestData['file_id'],
            $requestData['file_name'],
            $requestData['file_type'],
            $requestData['file_content'],
            $requestData['comments']
        );

        return response()->json([
            'added' => true
        ]);
    }

    /**
     * Display the specified basket
     *
     * @param string $projectId
     * @return JsonResource
     */
    public function show(string $projectId): JsonResource
    {
        $basket = $this->basketService->getBasket($projectId);
        return new BasketApiResource($basket);
    }
}
