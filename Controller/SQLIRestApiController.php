<?php

namespace SQLI\EzToolboxBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use EzSystems\EzPlatformAdminUi\Notification\FlashBagNotificationHandler;
use EzSystems\EzPlatformRest\Input\Handler\Json;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\View;
use SQLI\EzToolboxBundle\Form\EntityManager\EditElementType;
use SQLI\EzToolboxBundle\Services\EntityHelper;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ReflectionException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\SerializerInterface;

use function Sodium\add;

class SQLIRestApiController extends AbstractFOSRestController
{
    /** @var FlashBagNotificationHandler */
    protected $flashBagNotificationHandler;
    /** @var EntityManagerInterface */
    protected $entityManager;
    /** @var EntityHelper */
    protected $entityHelper;
    /** @var TranslatorInterface */
    protected $translator;

    /**
     * RestApiController constructor.
     * @param FlashBagNotificationHandler $flashBagNotificationHandler
     * @param EntityManagerInterface $entityManager
     * @param EntityHelper $entityHelper
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FlashBagNotificationHandler $flashBagNotificationHandler,
        EntityManagerInterface $entityManager,
        EntityHelper $entityHelper,
        TranslatorInterface $translator
    ) {
        $this->flashBagNotificationHandler = $flashBagNotificationHandler;
        $this->entityManager = $entityManager;
        $this->entityHelper = $entityHelper;
        $this->translator = $translator;
    }

    /**
     * @param string $fqcn
     * @param EntityHelper $entityHelper
     * @return Response
     * @throws ReflectionException
     * @Rest\Route("/{fqcn}/", methods={"GET"})
     */
    public function getJsonEntity(string $fqcn, EntityHelper $entityHelper): Response
    {
        $entity = $entityHelper->getEntity($fqcn, true);
        $response = new JsonResponse($entity);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        return $response;
    }

    /**
     * @param string $fqcn
     * @param string $compound_id
     * @param EntityHelper $entityHelper
     * @param SerializerInterface $serializer
     * @return Response
     * @Rest\Route("/{fqcn}/{compound_id}", methods={"GET"})
     */
    public function getJsonElement(
        string $fqcn,
        string $compound_id,
        EntityHelper $entityHelper,
        SerializerInterface $serializer
    ): Response {
        $element = $this->findEntityById($fqcn, $compound_id, $entityHelper);

        $elementArray = $serializer->normalize($element);
        $response = new JsonResponse($elementArray);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);
        return $response;
    }

    /**
     * @param Request $request
     * @param string $fqcn
     * @param EntityHelper $entityHelper
     * @param SerializerInterface $serializer
     * @return FormInterface|JsonResponse
     * @throws ReflectionException
     * @Rest\Route("/{fqcn}", methods={"POST"})
     */
    public function postJsonElement(
        Request $request,
        string $fqcn,
        EntityHelper $entityHelper,
        SerializerInterface $serializer
    ) {
        $entity = $entityHelper->getEntity($fqcn, false);
        $element = new $fqcn();
        $form = $this->createForm(EditElementType::class, $element, ['entity' => $entity, 'csrf_protection' => false]);

        $form->submit($request->request->all());
        if (false === $form->isValid()) {
            return $form;
        }

        $this->entityManager->persist($form->getData());
        $this->entityManager->flush();

        return new JsonResponse(
            [
                ["message" => "Resource " . $fqcn . " CREATED"],
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * @param string $fqcn
     * @param string $compound_id
     * @param EntityHelper $entityHelper
     * @return JsonResponse
     * @Rest\Route("/{fqcn}/{compound_id}", methods={"DELETE"})
     */
    public function deleteJsonElement(string $fqcn, string $compound_id, EntityHelper $entityHelper): JsonResponse
    {
        $element = $this->findEntityById($fqcn, $compound_id, $entityHelper);

        $this->entityManager->remove($element);
        $this->entityManager->flush();
        return new JsonResponse(
            ["message" => "Resource " . $fqcn . "DELETED"],
            JsonResponse::HTTP_OK
        );
    }

    /**
     * @param string $fqcn
     * @param string $compound_id
     * @param EntityHelper $entityHelper
     * @param Request $request
     * @return object|FormInterface
     * @Rest\Route("/{fqcn}/{compound_id}", methods={"PUT"})
     * @throws ReflectionException
     */
    public function updateJsonElement(string $fqcn, string $compound_id, EntityHelper $entityHelper, Request $request)
    {
        $entity = $entityHelper->getEntity($fqcn, false);
        $element = $this->findEntityById($fqcn, $compound_id, $entityHelper);

        $form = $this->createForm(
            EditElementType::class,
            $element,
            ['entity' => $entity, 'csrf_protection' => false]
        );

        $data = $request->request->all();
        $compound_id = json_decode($compound_id, true);
        $data = array_merge($compound_id, $data);
        $form->submit($data);

        if (false === $form->isValid()) {
            return $form;
        }

        $this->entityManager->flush();
        return $element;
    }

    /**
     * @param string $fqcn
     * @param string $compound_id
     * @param EntityHelper $entityHelper
     * @param Request $request
     * @Rest\Route("/{fqcn}/{compound_id}", methods={"PATCH"})
     * @return object|FormInterface
     * @throws ReflectionException
     */
    public function patchJsonElement(string $fqcn, string $compound_id, EntityHelper $entityHelper, Request $request)
    {
        $entity = $entityHelper->getEntity($fqcn, false);
        $element = $this->findEntityById($fqcn, $compound_id, $entityHelper);

        $form = $this->createForm(
            EditElementType::class,
            $element,
            ['entity' => $entity, 'csrf_protection' => false]
        );

        $form->submit($request->request->all(), false);

        if (false === $form->isValid()) {
            return $form;
        }

        $this->entityManager->flush();
        return $element;

    }

    /**
     * @param string $fqcn
     * @param string $compound_id
     * @param EntityHelper $entityHelper
     * @return object
     */
    private function findEntityById(string $fqcn, string $compound_id, EntityHelper $entityHelper): object
    {
        $compound_id = json_decode($compound_id, true);
        if (!empty($compound_id)) {
            $element = $entityHelper->findOneBy($fqcn, $compound_id);
            if (empty($element)) {
                throw new NotFoundHttpException();
            }
            return $element;
        }
    }
}
