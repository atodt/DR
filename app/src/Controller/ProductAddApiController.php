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

#[Route('/api/products', name: 'api_product_add', methods: ['POST'])]
class ProductAddApiController extends AbstractController
{
    public function __construct(
        private readonly LoggerInterface     $logger,
        private readonly ProductRepository   $repository,
        private readonly ValidatorInterface  $validator,
        private readonly SerializerInterface $serializer
    )
    {
    }

    /**
     * Handles the incoming request to create a new product or multiple products.
     *
     * This method processes the incoming JSON request data to create one or more product entities.
     * It validates the request data, creates new product entities, and persists them to the database.
     *
     * @param Request $request The HTTP request containing the JSON data for the product(s).
     *
     * @return JsonResponse A JSON response indicating the result of the operation.
     *                       - On success, returns a JSON response with the created product(s).
     *                       - On failure, returns a JSON response with an error message.
     */
    #[Cache(maxage: 0, public: false, mustRevalidate: true)]
    public function __invoke(Request $request): JsonResponse
    {
        $json_data = $request->getContent();
        try {
            $products = $this->serializer->deserialize($json_data, 'App\Entity\Product[]', 'json');
            if (is_array($products) && isset($products[0]) && $products[0] instanceof Product) {
                return $this->addMultipleProducts($products);
            }
            $product = $this->serializer->deserialize($json_data, 'App\Entity\Product', 'json');
            if ($product instanceof Product) {
                return $this->addSingleProduct($request, $product);
            }
        } catch (\Exception $e) {
            $this->logger->error('Error adding products: ' . $e->getMessage());

            return $this->json(['error' => 'Failed to add products.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Adds a single product to the database.
     *
     * This method handles the addition of a single product entity to the database.
     * It validates the product, persists it, and returns a JSON response indicating the result.
     *
     * @param Request $request The HTTP request containing the product data.
     * @param Product $product The product entity to be added.
     *
     * @return JsonResponse A JSON response indicating the result of the operation.
     *                      - On success, returns a JSON response with the created product.
     *                      - On failure, returns a JSON response with an error message.
     */
    private function addSingleProduct(Request $request, Product $product): JsonResponse
    {
        try {
            $result = $this->addProduct($product);
            if (!$result instanceof Product) {
                return $this->json([
                    'status' => 'error',
                    'errors' => $result,
                    '_links' => [
                        [
                            'href' => $request->getScheme() . '://' . $request->getHost() . $this->generateUrl('api_product_show', ['id' => $product->getId()]),
                            'method' => 'GET',
                            'rel' => 'product',
                            'title' => 'Show Product',
                        ],
                        [
                            'href' => $request->getScheme() . '://' . $request->getHost() . $this->generateUrl('api_product_add'),
                            'method' => 'GET',
                            'rel' => 'self',
                            'title' => 'Add Product',
                        ],
                    ],
                ], Response::HTTP_BAD_REQUEST);

            }
        } catch (\Exception $e) {
            $this->logger->error('Error adding product: ' . $e->getMessage());
            return $this->json(['error' => 'Failed to add product.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(
            [
                '_type' => 'Product',
                'product' => $result
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * Adds multiple products to the database.
     *
     * This method handles the addition of multiple product entities to the database.
     * It validates each product, persists them, and returns a JSON response indicating the result.
     *
     * @param array<Product> $products_arr An array of Product entities to be added.
     *
     * @return JsonResponse A JSON response indicating the result of the operation.
     *                      - On success, returns a JSON response with the created products.
     *                      - On failure, returns a JSON response with an error message.
     */
    private function addMultipleProducts(array $products_arr): JsonResponse
    {
        $added_products = [];
        $errors = [];
        try {
            foreach ($products_arr as $product) {
                $result = $this->addProduct($product);
                if (!$result instanceof Product) {
                    $errors[] = $result;
                    continue;
                }
                $added_products[] = $result;
            }
        } catch (\Exception $e) {
            $this->logger->error('Error adding multiple products: ' . $e->getMessage());
            return $this->json(['error' => 'Failed to add multiple products.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(
            [
                '_type' => 'ProductCollection',
                'products' => $added_products,
                'errors' => $errors,
            ],
            Response::HTTP_CREATED
        );
    }

    /**
     * Validates and adds a product to the database.
     *
     * This method validates the given product entity and, if valid, persists it to the database.
     * If there are validation errors, it returns an array of error messages.
     *
     * @param Product $product The product entity to be validated and added.
     *
     * @return mixed Returns the created product entity on success, or an array of validation error messages on failure.
     */
    private function addProduct(Product $product): mixed
    {
        $errors = $this->validator->validate($product);
        if (count($errors) > 0) {
            // There are validation errors
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $errorMessages;
        }

        return $this->repository->createProduct($product);
    }
}
