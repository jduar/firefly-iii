<?php

declare(strict_types=1);

namespace FireflyIII\Api\V1\Controllers\Models\TransactionTemplate;

use FireflyIII\Api\V1\Controllers\Controller;
use FireflyIII\Repositories\TransactionTemplate\TransactionTemplateRepositoryInterface;
use FireflyIII\Transformers\TransactionTemplateTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection as FractalCollection;

/**
 * Class ListController
 */
final class ListController extends Controller
{
    private TransactionTemplateRepositoryInterface $repository;

    /**
     * ListController constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware(function ($request, $next) {
            $this->repository = app(TransactionTemplateRepositoryInterface::class);
            $this->repository->setUser(auth()->user());

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $manager  = $this->getManager();
        $pageSize = $this->parameters->get('limit');

        // get list of templates. Count it and split it.
        $collection = $this->repository->getTemplates();
        $count      = $collection->count();
        $templates  = $collection->slice(($this->parameters->get('page') - 1) * $pageSize, $pageSize);

        // make paginator:
        $paginator = new LengthAwarePaginator($templates, $count, $pageSize, $this->parameters->get('page'));
        $paginator->setPath(route('api.v1.transaction-templates.index').$this->buildParams());

        /** @var TransactionTemplateTransformer $transformer */
        $transformer = app(TransactionTemplateTransformer::class);
        $transformer->setParameters($this->parameters);

        $resource = new FractalCollection($templates, $transformer, 'transaction_templates');
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));

        return response()->json($manager->createData($resource)->toArray())->header('Content-Type', self::CONTENT_TYPE);
    }
}
