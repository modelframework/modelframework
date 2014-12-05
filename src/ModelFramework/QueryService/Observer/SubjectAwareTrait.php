<?php
/**
 * Class SubjectAwareTrait
 * @package ModelFramework\QueryService\Observer
 * @author  Vladimir Pasechnik vladimir.pasechnik@gmail.com
 * @author  Stanislav Burikhin stanislav.burikhin@gmail.com
 */

namespace ModelFramework\QueryService\Observer;

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