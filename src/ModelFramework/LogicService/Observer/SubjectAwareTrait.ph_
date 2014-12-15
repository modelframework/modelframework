<?php
/**
 * Created by PhpStorm.
 * User: PROG-3
 * Date: 02.12.2014
 * Time: 17:05
 */

namespace ModelFramework\LogicService\Observer;

trait SubjectAwareTrait
{

    /**
     * @var SplSubject
     */
    private $_subject = null;

    /**
     * @param \SplSubject $subject
     *
     * @return $this
     */
    public function setSubject( \SplSubject $subject )
    {
        $this->_subject = $subject;
        return $this;
    }

    /**
     * @return SplSubject
     */
    public function getSubject( )
    {
        return $this->_subject;
    }

} 