<?php
/**
 * Created by PhpStorm.
 * User: Nam Ngo
 * Date: 2020-05-07
 * Time: 14:23
 */

namespace StCommonService\Service\Pagination;

use Doctrine\ORM\Tools\Pagination\Paginator as BasePaginator;

class Paginator extends BasePaginator
{
    /**
     * @var int
     */
    private $currentPage = 1;

    /**
     * @var int
     */
    private $pageSize = 5;


    /**
     * Get total amount of pages
     *
     * @return int
     */
    public function getTotalPages(): int
    {
        return (int) ceil($this->count() / $this->pageSize);
    }

    /**
     * Get offset to fetch the first result
     *
     * @return int
     */
    public function getOffset(): int
    {
        return $this->pageSize * ($this->currentPage - 1);
    }

    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage(int $currentPage): void
    {
        $this->currentPage = $currentPage < 1 ? 1 : $currentPage;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    /**
     * @param int $pageSize
     */
    public function setPageSize(int $pageSize): void
    {
        $this->pageSize = $pageSize;
    }
}