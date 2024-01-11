<?php

declare(strict_types=1);

namespace App\Validator;

use App\Repository\WidgetRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class WidgetToggleValidator
{
    private const MAX_COLUMNS = 6;

    public function __construct(private WidgetRepository $widgetRepository)
    {
    }

    public function validateRequestedWidgets(?array $widgets): void
    {
        if ($widgets === null) {
            throw new BadRequestHttpException('Invalid or missing widgets in request');
        }
    }

    /**
     * @param array<string> $requestedWidgets
     */
    public function validateWidgets(array $requestedWidgets): void
    {
        $validAltNames = $this->getValidWidgetAltNames($requestedWidgets);

        $diff = array_diff($requestedWidgets, $validAltNames);
        if ($diff !== []) {
            throw new NotFoundHttpException(sprintf('Invalid widgets: %s', implode(', ', $diff)));
        }
    }

    /**
     * @param array<string> $widgets
     */
    public function validateWidgetSizeSum(array $widgets): void
    {
        if ($this->calculateTotalSize($widgets) > self::MAX_COLUMNS) {
            $message = sprintf('Invalid widget size sum. Max columns: %d', self::MAX_COLUMNS);
            throw new UnprocessableEntityHttpException($message);
        }
    }

    /**
     * @param array<string> $requestedWidgets
     *
     * @return array<string>
     */
    private function getValidWidgetAltNames(array $requestedWidgets): array
    {
        $widgetsFromDB = $this->widgetRepository->findBy(['altName' => $requestedWidgets]);

        return array_map(static fn ($widget): ?string => $widget->getAltName(), $widgetsFromDB);
    }

    /**
     * @param array<string> $widgets
     */
    private function calculateTotalSize(array $widgets): int
    {
        return array_sum(array_map(static fn ($widget): int => (int) substr($widget, -1), $widgets));
    }
}
