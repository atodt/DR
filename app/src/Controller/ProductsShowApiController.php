<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products/{id<\d+>}', name: 'api_product_show', methods: ['GET'])]
class ProductsShowApiController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private readonly LoggerInterface   $logger,
        private readonly ProductRepository $repository,
    )
    {
    }

    /**
     * Handles the request to show a product by its ID.
     *
     * @param Request $request the HTTP request object containing the product ID
     *
     * @return JsonResponse a JSON response containing the product details or an error message
     */
    #[Cache(maxage: 0, public: false, mustRevalidate: true)]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $id = $request->get('id');
            if (!is_numeric($id)) {
                return $this->json(['error' => 'Invalid product ID.'], Response::HTTP_BAD_REQUEST);
            }

            $product = $this->repository->findById((int)$id);
            if (!$product instanceof Product) {
                return $this->sendNotFoundResponse();
            }

            return $this->json(
                [
                    '_type' => 'Product',
                    'product' => $product,
                    '_links' => [
                        [
                            'href' => $request->getScheme() . '://' . $request->getHost() . $this->generateUrl('api_product_show', ['id' => $product->getId()]),
                            'method' => 'GET',
                            'rel' => 'self',
                            'title' => 'Product',
                        ],
                    ],
                ],
                Response::HTTP_OK
            );
        } catch (\Exception $e) {
            $this->logger->error('Error showing product: ' . $e->getMessage());

            return $this->json(['error' => 'Failed to show product.' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
