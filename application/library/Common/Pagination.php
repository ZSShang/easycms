<?php

/**
 * Created by PhpStorm.
 * User: micle
 * Date: 2017/8/11
 * Time: 18:00
 */
class Common_Pagination
{
    /** @var string the pagination base url */
    protected $_base_url;

    /** @var int the pagination total page */
    protected $_pagetotal;

    /** @var int the pagination current page */
    protected $_cur_page;

    /** @var int the pagination data numbers */
    protected $_pagesize;

    /** @var array the pagination request filter */
    protected $_query_str = [];

    /** @var int the pagination show link numbers */
    protected $_show_link_nums = 5;

    /** @var string the pagination page link */
    protected $_page_link = '';

    public function __construct()
    {
    }

    /**
     * 初始化配置
     */
    public function config()
    {
        if (func_num_args() > 1) {
            $arguments = func_get_args();
            for ($i = 0; $i < func_num_args(); $i++) {
                $this->setOption('_'.key($arguments[$i]),current($arguments[$i]));
            }
        } else {
            if (is_array(func_get_arg(0))) {
                foreach (func_get_arg(0) as $k => $item) {
                    $this->setOption('_'.$k,$item);
                }
            }
        }
    }

    /*
     * 创建分页相关信息
     * @param string 确定返回数据形式 value (html | array)
     * @return mixed
     */
    public function create_links($type = 'html')
    {
        if (!in_array($type, ['html', 'array'])) {
            return ['type' => 'error', 'msg' => 'Invalid Params!'];
        }

        if (isset($this->_pagesize) && !empty($this->_pagesize)) {
            $query_all = array_merge($this->_query_str, ['pagesize' => $this->_pagesize], ['page' => '']);
        } else {
            $query_all = array_merge($this->_query_str, ['page' => '']);
        }

        if ('html' == $type) {
            $query_link = "?" . http_build_query($query_all);
        } else {
            $query_link = http_build_query($query_all);
        }

        if ('array' === $type) {
            $link_arr = ['base_url' => $this->_base_url, 'cur_page' => $this->_cur_page, 'loop_page' => []];
        }

        if (1 < $this->_cur_page) {
            if ('html' === $type) {
                $this->_page_link .= '<a href="' . $this->_base_url . $query_link . '1">首页</a>&nbsp;&nbsp;<a href="' . $this->_base_url . $query_link . ($this->_cur_page - 1) . '">上一页</a>';
            }

            if ('array' === $type) {
                $link_arr['first_page'] = $query_link . '1';
                $link_arr['pre_page'] = $query_link . ($this->_cur_page - 1);
            }
        }

        if ($this->_pagetotal <= $this->_show_link_nums) {

            for ($i = 1; $i <= $this->_pagetotal; $i++) {
                if ('html' === $type) {
                    if ($i == $this->_cur_page) {
                        $this->_page_link .= '&nbsp;&nbsp;<span>' . $i . '</span>&nbsp;&nbsp;';
                    } else {
                        $this->_page_link .= '&nbsp;&nbsp;<a href="' . $this->_base_url . $query_link . $i . '">' . $i . '</a>&nbsp;&nbsp;';
                    }
                }

                if ('array' === $type) {
                    $link_arr['loop_page'][$i] = $query_link . $i;
                }
            }

        } else {

            $step = floor($this->_show_link_nums / 2);

            if ($this->_cur_page <= $this->_show_link_nums - $step) {

                for ($i = 1; $i <= $this->_show_link_nums; $i++) {
                    if ('html' === $type) {
                        if ($i == $this->_cur_page) {
                            $this->_page_link .= '&nbsp;&nbsp;<span>' . $i . '</span>&nbsp;&nbsp;';
                        } else {
                            $this->_page_link .= '&nbsp;&nbsp;<a href="' . $this->_base_url . $query_link . $i . '">' . $i . '</a>&nbsp;&nbsp;';
                        }
                    }

                    if ('array' === $type) {
                        $link_arr['loop_page'][$i] = $query_link . $i;
                    }
                }

            } else {

                if ($this->_cur_page + $step > $this->_pagetotal) {

                    for ($i = $this->_pagetotal - $this->_show_link_nums + 1; $i <= $this->_pagetotal; $i++) {
                        if ('html' === $type) {
                            if ($i == $this->_cur_page) {
                                $this->_page_link .= '&nbsp;&nbsp;<span>' . $i . '</span>&nbsp;&nbsp;';
                            } else {
                                $this->_page_link .= '&nbsp;&nbsp;<a href="' . $this->_base_url . $query_link . $i . '">' . $i . '</a>&nbsp;&nbsp;';
                            }
                        }

                        if ('array' === $type) {
                            $link_arr['loop_page'][$i] = $query_link . $i;
                        }
                    }

                } else {
                    for ($i = $this->_cur_page - $step; $i <= $this->_cur_page + $step; $i++) {
                        if ('html' === $type) {
                            if ($i == $this->_cur_page) {
                                $this->_page_link .= '&nbsp;&nbsp;<span>' . $i . '</span>&nbsp;&nbsp;';
                            } else {
                                $this->_page_link .= '&nbsp;&nbsp;<a href="' . $this->_base_url . $query_link . $i . '">' . $i . '</a>&nbsp;&nbsp;';
                            }
                        }

                        if ('array' === $type) {
                            $link_arr['loop_page'][$i] = $query_link . $i;
                        }
                    }
                }

            }
        }

        if ($this->_cur_page < $this->_pagetotal) {
            if ('html' === $type) {
                $this->_page_link .= '<a href="' . $this->_base_url . $query_link . ($this->_cur_page + 1) . '">下一页</a>&nbsp;&nbsp;<a href="' . $this->_base_url . $query_link . $this->_pagetotal . '">尾页</a>';
            }

            if ('array' === $type) {
                $link_arr['next_page'] = $query_link . ($this->_cur_page + 1);
                $link_arr['last_page'] = $query_link . $this->_pagetotal;
            }
        }

        if ('html' === $type) {
            return $this->_page_link;
        }

        if ('array' === $type) {
            return $link_arr;
        }

        return false;
    }

    /*
     * 设置分页配置(外部)
     * @param string 属性名
     * @param string 属性值
     * @return bool
     */
    public function __set($name, $value)
    {
        $this->$name = $value;
        return true;
    }

    /*
     * 设置分页配置(实例化时)
     * @param string 属性名
     * @param string 属性值
     * @return bool
     */
    private function setOption($name,$value)
    {
        $this->$name = $value;
    }
}