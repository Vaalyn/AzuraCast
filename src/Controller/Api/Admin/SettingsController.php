<?php

namespace App\Controller\Api\Admin;

use App\Entity;
use App\Exception\ValidationException;
use App\Http\Response;
use App\Http\ServerRequest;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SettingsController
{
    protected EntityManagerInterface $em;

    protected Serializer $serializer;

    protected ValidatorInterface $validator;

    protected Entity\Repository\SettingsTableRepository $settingsTableRepo;

    protected Entity\Settings $settings;

    public function __construct(
        EntityManagerInterface $em,
        Entity\Repository\SettingsTableRepository $settingsTableRepo,
        Entity\Settings $settings,
        Serializer $serializer,
        ValidatorInterface $validator
    ) {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->validator = $validator;

        $this->settingsTableRepo = $settingsTableRepo;
        $this->settings = $settings;
    }

    /**
     * @OA\Get(path="/admin/settings",
     *   tags={"Administration: Settings"},
     *   description="List the current values of all editable system settings.",
     *   @OA\Response(response=200, description="Success",
     *     @OA\JsonContent(ref="#/components/schemas/Settings")
     *   ),
     *   @OA\Response(response=403, description="Access denied"),
     *   security={{"api_key": {}}},
     * )
     *
     * @param ServerRequest $request
     * @param Response $response
     */
    public function listAction(ServerRequest $request, Response $response): ResponseInterface
    {
        return $response->withJson($this->serializer->normalize($this->settings, null));
    }

    /**
     * @OA\Put(path="/admin/settings",
     *   tags={"Administration: Settings"},
     *   description="Update settings to modify any settings provided.",
     *   @OA\RequestBody(
     *     @OA\JsonContent(ref="#/components/schemas/Settings")
     *   ),
     *   @OA\Response(response=200, description="Success",
     *     @OA\JsonContent(ref="#/components/schemas/Api_Status")
     *   ),
     *   @OA\Response(response=403, description="Access denied"),
     *   security={{"api_key": {}}},
     * )
     *
     * @param ServerRequest $request
     * @param Response $response
     *
     * @throws ValidationException
     */
    public function updateAction(ServerRequest $request, Response $response): ResponseInterface
    {
        $this->settingsTableRepo->writeSettings($request->getParsedBody());

        return $response->withJson(new Entity\Api\Status());
    }
}
