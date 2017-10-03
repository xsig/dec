<?php
namespace Dec\models;
class DbUtils {
	public function getNextSeq($name) {
			$m = new MongoClient("mongodb://localhost:27017");	
			$db = $m->decdb;
			$collection = $db->counters;
			$record = NULL;
			try {
				$record = $db->execute(
					'db.counters.findAndModify(
									{
										query: { _id: "'.$name.'" },
										update: { $inc: { seq: 1 } },
										new: true
									}
								)');
	//			$ret = $collection->findAndModify(
	//				array(  "query"  =>array( "_id" => $name),
	//						"update" =>array('$inc'=> array("seq"=> 1)),
	//						"new" => true
	//				)
	//			);
			} catch(MongoResultException $e) {
	//			echo "<br>Excepxion:<br>";
	//			echo $e->getCode(), " : ", $e->getMessage(), "\n";
	//			var_dump($e->getDocument());
	//			echo "<br>Fin Excepxion:<br>";
				$record['retval']['seq'] = 0;
			}
			return $record['retval']['seq'];
	}
}
