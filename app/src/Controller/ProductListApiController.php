<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products', name: 'api_product_list', methods: ['GET'])]
class ProductListApiController extends AbstractController
{
    /**
 * Constructor for the ProductListApiController.
 *
 * @param LoggerInterface $logger The logger service for logging errors and information.
 * @param ProductRepository $repository The repository service for accessing product data.
 */
public function __construct(
    private readonly LoggerInterface   $logger,
    private readonly ProductRepository $repository
)
{
}

    /**
     * Handles the request to fetch the list of products.
     *
     * @param Request $request The HTTP request object.
     * 
     * @return JsonResponse A JSON response containing the list of products or an error message.
     */
    #[Cache(maxage: 0, public: false, mustRevalidate: true)]
    public function __invoke(Request $request): JsonResponse
    {
        $this->logger->info('Fetching products');
        try {
            $products = $this->repository->findAll();
            return $this->json(
                [
                    '_type' => 'ProductCollection',
                    'items' => $products,
                    '_links' => [
                        [
                            'href' => $request->getScheme() . '://' . $request->getHost() . $this->generateUrl('api_product_list'),
                            'method' => 'GET',
                            'rel' => 'self',
                            'title' => 'Products',
                        ],
                    ],
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            $this->logger->error('Error fetching products: ' . $e->getMessage());

            return $this->json(['error' => 'Failed to get products ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
