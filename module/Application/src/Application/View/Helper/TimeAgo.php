<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\View\Helper\InlineScript as Inline;

class TimeAgo extends AbstractHelper {
    const FORMAT = '<abbr class="timeago" title="%1$s">%1$s</abbr>';
    const READY = '
        jQuery(document).ready(function() {
            jQuery("abbr.timeago").timeago();
        });';
    private $prepared = false;
    private $script = '/js/timeago.js';

    /**
     * @param \DateTime|null $time
     * @return string|TimeAgo
     */
    public function __invoke(\DateTime $time = null) {
        if ($time !== null) {
            if (!$this->prepared) {
                $this->view->headScript()->appendFile($this->script);
                $this->view->inlineScript(Inline::SCRIPT, self::READY);
                $this->prepared = true;
            }
            return sprintf(self::FORMAT, $time->format(\DateTime::ISO8601));
        }
        return $this;
    }

    /**
     * @param string $path
     * @return TimeAgo
     */
    public function setScriptPath($path) {
        $this->script = $path;
        return $this;
    }
}
