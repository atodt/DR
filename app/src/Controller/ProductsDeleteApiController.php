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

#[Route('/api/products/{id<\d+>}', name: 'api_product_delete', methods: ['DELETE'])]
class ProductsDeleteApiController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly ProductRepository $repository,
    ) {
    }

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
            } else {
                $this->repository->removeProduct($product);
            }

            return $this->json(
                $id,
                Response::HTTP_NO_CONTENT
            );
        } catch (\Exception $e) {
            $this->logger->error('Error deleting product: '.$e->getMessage());

            return $this->json(['error' => 'Failed to delete product.'.$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
