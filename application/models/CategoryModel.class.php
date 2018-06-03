<?php
//商品分类模型
class CategoryModel extends Model{
	//获取所有的商品分类
	public function getCats(){
		$sql = "SELECT * FROM {$this->table}";
		$cats = $this->db->getAll($sql);
		return $this->tree($cats);
	}
	/**
	 * 重新排序
	 * @param  array  $arr [要排序的数组]
	 * @param  integer $pid [父id]
	 * @return array     [排好序的数组]
	 */
	
	public function tree($arr,$pid = 0,$level = 0){
		$res = array();
		foreach ($arr as $v) {
			if ($v['parent_id'] == $pid) {
				//说明找到，先保存，然后递归查找
				$v['level'] = $level;
				$res[] = $v;
				$res = array_merge($res,$this->tree($arr,$v['cat_id'],$level+1));
			}
		}
		return $res;
	}
	//docblockr
	/**
	 * 获取指定分类所有的后代分类id，包括它自己
	 * @param  int $cat_id [分类id]
	 * @return array       [所有后代分类id]
	 */
	public function getSubIds($cat_id){
		$sql = "SELECT * FROM {$this->table}";
		$cats = $this->db->getAll($sql);
		$cats = $this->tree($cats,$cat_id); //二维数组
		$res = array();
		foreach ($cats as $v) {
			$res[] = $v['cat_id'];
		}
		//将自己也追加到数组中
		$res[] = $cat_id;
		return $res;
	}

	/**
	 * 构造嵌套结构的多维数组
	 * @param  [array]  $arr [要处理的二维数组]
	 * @param  integer $pid [从哪个节点开始]
	 * @return [array]       [处理之后的多维数组]
	 */
	public function child($arr,$pid = 0) {
		$res = array();
		foreach ($arr as $v) {
			if ($v['parent_id'] == $pid) {
				//找到了，继续找，递归
				$childs = $this->child($arr,$v['cat_id']);
				//将找到的结果保存到当前数组的下标为child的元素中
				$v['child'] = $childs;
				$res[] = $v;
			}
		}
		return $res;
	}

	public function frontCats(){
		$sql = "SELECT * FROM {$this->table}";
		$cats = $this->db->getAll($sql);
		return $this->child($cats);
	}
}