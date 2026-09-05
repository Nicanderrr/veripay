<?php

namespace App\Services;

use App\Models\Product;
use Milon\Barcode\DNS2D;

class ProductQrCode
{
    public function svgFromText(string $text): string
    {
        return (new DNS2D())->getBarcodeSVG($text, 'QRCODE', 8, 8);
    }

    public function dataUriFromText(string $text): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode($this->svgFromText($text));
    }

    public function svg(Product $product): string
    {
        return $this->svgFromText($product->barcode);
    }

    public function dataUri(Product $product): string
    {
        return $this->dataUriFromText($product->barcode);
    }
}
