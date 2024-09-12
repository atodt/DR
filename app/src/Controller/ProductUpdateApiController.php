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
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/products/{id<\d+>}', name: 'api_product_update', methods: ['PUT'])]
class ProductUpdateApiController extends AbstractController
{
    use ControllerTrait;

    public function __construct(
        private readonly LoggerInterface     $logger,
        private readonly ProductRepository   $repository,
        private readonly ValidatorInterface  $validator,
        private readonly SerializerInterface $serializer,
    ) {
    }

    /**
     * Create a new product.
     *
     * This method processes the incoming request to create a new product.
     * It validates the request data, creates a new product entity, and persists it to the database.
     */
    #[Cache(maxage: 0, public: false, mustRevalidate: true)]
    public function __invoke(Request $request): JsonResponse
    {
        $json_data = $request->getContent();
        try {
            $product_id = $request->get('id');
            if (!is_numeric($product_id)) {
                return $this->json(['error' => 'Invalid product ID.'], Response::HTTP_BAD_REQUEST);
            }
            $product = $this->repository->findById((int)$product_id);
            if (!$product instanceof Product) {
                return $this->sendNotFoundResponse();
            }

            $product_update = $this->serializer->deserialize($json_data, 'App\Entity\Product', 'json');
            if (!$product_update instanceof Product) {
                return $this->json(['error' => 'Invalid product data.'], Response::HTTP_BAD_REQUEST);
            }

            return $this->updateProduct($product, $product_update);
        } catch (\Exception $e) {
            $this->logger->error('Error updating product: ' . $e->getMessage());

            return $this->json(['error' => 'Failed to update product.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function updateProduct(Product $product, Product $product_update): JsonResponse
    {
        if (null !== $product_update->getTitle()) {
            $product->setTitle($product_update->getTitle());
        }
        if (null !== $product_update->getDescription()) {
            $product->setDescription($product_update->getDescription());
        }
        if (null !== $product_update->getCategory()) {
            $product->setCategory($product_update->getCategory());
        }
        if (null !== $product_update->getState()) {
            $product->setState('true' == $product_update->getState());
        }

        $errors = $this->validator->validate($product);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        try {
            $updated_product = $this->repository->saveProduct($product);
        } catch (\Exception $e) {
            $this->logger->error('Error saving product: ' . $e->getMessage());

            return $this->json(['error' => 'Failed to save product.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(
            [
                '_type' => 'Product',
                'product' => $updated_product,
            ],
            Response::HTTP_OK
        );
    }
}
