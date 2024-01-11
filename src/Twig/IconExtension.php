<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class IconExtension extends AbstractExtension
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    /**
     * @return array<TwigFunction> returns an array of TwigFunction objects
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('heroicon', [$this, 'getHeroicon'], ['is_safe' => ['html']]),
            new TwigFunction('ionicon', [$this, 'getIonicon'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param array<string, string> $options
     */
    public function getHeroicon(
        string $iconName,
        string $class = '',
        string $fillColor = 'currentColor',
        string $iconType = 'solid',
        array $options = []
    ): string {
        $iconPath = __DIR__.sprintf('/../../node_modules/heroicons/24/%s/%s.svg', $iconType, $iconName);

        return $this->getSvgContent($iconPath, $iconName, $class, $options, $fillColor);
    }

    /**
     * @param array<string, string> $options
     */
    public function getIonicon(
        string $iconName,
        string $class = '',
        string $fillColor = 'currentColor',
        string $iconType = 'outline',
        array $options = []
    ): string {
        $iconName = sprintf('%s-%s', $iconName, $iconType);
        $iconPath = __DIR__.sprintf('/../../node_modules/ionicons/dist/svg/%s.svg', $iconName);

        return $this->getSvgContent($iconPath, $iconName, $class, $options, $fillColor);
    }

    /**
     * @param array<string, string> $options
     *
     * @throws \RuntimeException
     */
    private function getSvgContent(
        string $iconPath,
        string $iconName,
        string $class,
        array $options,
        string $fillColor
    ): string {
        if (! file_exists($iconPath)) {
            throw new \RuntimeException($this->translator->trans('Icon not found: '.$iconName));
        }

        $svgContent = file_get_contents($iconPath);
        if (false === $svgContent) {
            $message = $this->translator->trans('Unable to read the contents of the icon: '.$iconName);
            throw new \RuntimeException($message);
        }

        if (str_contains($iconPath, 'heroicons')) {
            $svgContent = str_replace('fill="currentColor"', 'fill="'.$fillColor.'"', $svgContent);
        } else {
            $svgContent = str_replace('stroke="currentColor"', 'stroke="'.$fillColor.'"', $svgContent);
        }

        $svgAttributes = $this->generateSvgAttributes($class, $options);

        return str_replace('<svg', sprintf('<svg%s', $svgAttributes), $svgContent);
    }

    /**
     * @param array<string, string> $options
     */
    private function generateSvgAttributes(string $class, array $options): string
    {
        $attributes = sprintf(' class="%s"', $class);
        foreach ($options as $attr => $value) {
            $attributes .= sprintf(' %s="%s"', $attr, $value);
        }

        return $attributes;
    }
}
