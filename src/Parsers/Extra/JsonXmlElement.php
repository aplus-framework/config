<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Config Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Aplus\Config\Parsers\Extra;

/**
 * Class JsonXmlElement.
 *
 * @see \Aplus\Config\Parsers\XmlParser
 *
 * @package config
 */
class JsonXmlElement extends \SimpleXMLElement implements \JsonSerializable
{
    /**
     * @return array<mixed>|string
     */
    public function jsonSerialize() : array | string
    {
        $data = [];
        foreach ($this as $name => $element) {
            $name = (string) $name;
            if (!isset($data[$name])) {
                $data[$name] = $element;
                continue;
            }
            if (!\is_array($data[$name])) {
                $data[$name] = [$data[$name]];
            }
            $data[$name][] = $element;
        }
        $text = \trim((string) $this);
        if (($text !== '') && empty($data)) {
            $data = $text;
        }
        return $data;
    }
}
