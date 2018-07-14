<?php
    require_once('Frameworks/knn-master/src/Fieg/kNN/DataSet.php');
    require_once('Frameworks/knn-master/src/Fieg/kNN/Node.php');
    require_once('Frameworks/knn-master/src/Fieg/kNN/Schema.php');

    use Fieg\kNN\Node;
    
    class RecommendationsEngine{
        /*initaliaze KNN objects*/
        private $schema = null;
        private $dataSet = null;
        /* default class constructor*/
        /* @params : schemaObjects String[]*/
        function __construct($schemaObjects) {
            $this->schema = new \Fieg\kNN\Schema();
            $numberOfObjects = 0;
            foreach($schemaObjects as $schemaObject){
                $this->schema->addField($schemaObject);
                ++$numberOfObjects;
            }
            $this->dataset = new \Fieg\kNN\DataSet($numberOfObjects, $this->schema);
        }

        /* function AddObjectToDataSet*/
        /* @params : object Array[]*/
        public function AddObjectToDataSet($object){
            $this->dataset->add(new Node($object));
        }

        /* function GuessRecomandation*/
        /* @params : object Array[], field String*/
        /* return String*/
        public function GuessRecomandation($object, $field){
            return $this->dataset->guess(new Node($object), $field);
        }
    }
?>