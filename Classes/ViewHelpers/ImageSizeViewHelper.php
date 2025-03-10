<?php

namespace GeorgRinger\News\ViewHelpers;

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

/**
 * This file is part of the "news" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

/**
 * Class ImageSizeViewHelper
 */
class ImageSizeViewHelper extends AbstractViewHelper implements ViewHelperInterface
{
    use CompileWithRenderStatic;

    /**
     * Initialize arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('property', 'string', 'either width or height', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return int
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): int {
        $value = 0;
        $tsfe = static::getTypoScriptFrontendController();
        if (!is_null($tsfe)) {
            switch ($arguments['property']) {
                case 'width':
                    $value = $tsfe->lastImageInfo[0];
                    break;
                case 'height':
                    $value = $tsfe->lastImageInfo[1];
                    break;
                case 'size':
                    /** @var ProcessedFile $processedImage */
                    $processedImage = $tsfe->lastImageInfo['processedFile'];
                    if ($processedImage) {
                        $value = $processedImage->getSize();
                    } elseif ($originalFile = $tsfe->lastImageInfo['originalFile']) {
                        /** @var File $originalFile */
                        $value = $originalFile->getSize();
                    }
                    break;
                default:
                    throw new \RuntimeException(sprintf('The value "%s" is not supported in ImageSizeViewHelper', $arguments['property']));
            }
        }
        return $value;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected static function getTypoScriptFrontendController(): TypoScriptFrontendController
    {
        return $GLOBALS['TSFE'];
    }
}
