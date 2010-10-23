<?php
/**
 *
 * This is a glorious model/mapper generator.
 *
 * Uses a provided xml file to generate working code for db and business models.
 *
 * @author  darrylc
 * @since   2010-10-01
 * @version $Id:$
 *
 */

// Initialize the application path and autoloading
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
set_include_path(implode(PATH_SEPARATOR, array(APPLICATION_PATH . '/../library', get_include_path(),)));

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->registerNamespace('Insta_');

// Define some CLI options
$getopt = new Zend_Console_Getopt(array(
          'file|f=s'   => 'Use this xml file for input',
          'output|o=s' => 'Output directory - will use Zend Framework naming.',
          'env|e=s'    => 'Environment override, default is "development"',
          'help|h'     => 'Help -- this awesome usage message'));
try {
    $getopt->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    // Bad options passed: report usage
    echo $e->getUsageMessage();
    return false;
}

// If help requested, report usage message
if ($getopt->getOption('h')) {
    echo $getopt->getUsageMessage();
    return true;
}

DEFINE('MODEL_NAMESPACE', 'Insta_Model_');
DEFINE('MAPPER_NAMESPACE', 'Insta_Mapper_');

// Initialize values based on presence or absence of CLI options
$env = $getopt->getOption('e');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (null === $env) ? 'development' : $env);

// Initialize Zend_Application
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');

// Initialize bootstrap resources
$bootstrap = $application->getBootstrap();

/**
 * do stuff here
 */

if (! is_dir($getopt->getOption('o'))) {
    echo " *** Output directory is not a directory.\n\n";
    return false;
}
$output = $getopt->getOption('o');

if (! is_file($getopt->getOption('f'))) {
    echo " *** Input file is not a file.\n\n";
    return false;
}

// load the xml file and do stuff.
$xml = simplexml_load_file($getopt->getOption('f'));

$modelList = array();

foreach ($xml as $model) {
    // reset counter here multi sources are ok.
    $i = 0;
    $mapperAttr  = $model->attributes();
    $mapperName  = (string)$mapperAttr['name'];
    $masterRead  = (string)$mapperAttr['readmaster'] ? (string)$mapperAttr['readmaster'] : 'false';
    $sources     = array();
    $primaryKey  = array();
    $foreignKeys = array();
    $fieldData   = array();
    $modelFields = array();

    $fkModel     = null;

    if ($model->foreignkey) {
        $foreignAttr = $model->foreignkey->attributes();
        $fkModel     = (string)$foreignAttr['model'];
    }

    if ($fkModel != '') {
        foreach ($model->foreignkey->reference as $ref) {
            $refAttr  = $ref->attributes();
            $foreignKeys[$fkModel]['foreign'] = (string)$refAttr['foreign'];
            $foreignKeys[$fkModel]['local'][] = (string)$refAttr['local'];
        }
    }


    foreach ($model->source as $source) {
        $sourceAttr = $source->attributes();
        $sourceName = (string)$sourceAttr['name'];
        $sourceType = (string)$sourceAttr['type'];

        if ($sourceType == 'db') {
            // we need a query
            $sources[$i]['query']  = (string)$source->query->sql;
            $sources[$i]['db']     = (string)$source->query->db;
        } elseif ($sourceType == 'uc') {
            // tell UC where to go
            $sources[$i]['query'] = $sourceName;
        } else {
            // kerblamo
            echo "!!! Invalid source type $sourceType provided for source $sourceName\n\n";
            exit(99);
        }

        $sources[$i]['type']  = $sourceType;
        $sources[$i]['name']  = $sourceName;

        foreach ($source->fields->field as $field) {
            $fieldAttr = $field->attributes();
            $i++;
            $target = (string)$fieldAttr['modelField'];
            $from   = (string)$fieldAttr['sourceField'];
            $fieldData[$from]                  = $from;
            $fieldData[$target]['source']      = $sourceType;
            $fieldData[$target]['sourceName']  = $sourceName;
            $fieldData[$target]['sourceField'] = $from;
            $fieldData[$target]['sourceType']  = (string)$fieldAttr['sourceType'];
            $fieldData[$target]['modelField']  = (string)$fieldAttr['modelField'];
            $fieldData[$target]['modelType']   = (string)$fieldAttr['modelType'];
            $fieldData[$target]['primary']     = (bool)$fieldAttr['primary'];
            if ((bool)$fieldAttr['primary']) {
                $primaryKey[$sourceName] =  (string)$fieldAttr['modelField'];
            }
            $modelFields[] = $target;
        }
    }

    $modelList[$mapperName]['foreignKey']  = $foreignKeys;
    $modelList[$mapperName]['primaryKey']  = $primaryKey;
    $modelList[$mapperName]['modelFields'] = $modelFields;
    $modelList[$mapperName]['fieldData']   = $fieldData;
    $modelList[$mapperName]['sources']     = $sources;
    $modelList[$mapperName]['masterRead']  = $masterRead;

}
// Integrity checks (make sure fk's exist in parent models, etc.)
$errors = false;
foreach ($modelList as $mapperName => $modelInfo) {
    if (count($modelInfo['foreignKey']) > 0) {
        // fk check
        $fkModel = key($modelInfo['foreignKey']);
        // make sure foreign model exists
        if ($modelList[$fkModel]) {
            // make sure
            $fk = $modelInfo['foreignKey'][$fkModel]['foreign'];
            $lk = $modelInfo['foreignKey'][$fkModel]['local'];
            // a) local exists in this model
            if ($fk != $modelList[$fkModel]['fieldData'][$fk]) {
                echo "!! foreign key [ $fk ] does not exist in: " . $fkModel . "\n";
                $errors = true;
            }
            // b) foreign exists in foreign model
            foreach ($lk as $key) {
                if (
                ! isset($modelList[$mapperName]['fieldData'][$key]) ||
                $key != $modelList[$mapperName]['fieldData'][$key]) {
                    echo "!! local key [ $key ] does not exist in: " . $mapperName . "\n";
                    $errors = true;
                }
            }
        } else {
            echo "!! foreign model not defined: " . $fkModel . " (definition is case sensative) \n";
            $errors = true;
        }

        if (! $errors) {
            echo "++ $mapperName passes FK Check.\n";
        }
    } else {
        echo "++ $mapperName passes FK Check.\n";
    }
}

// generate code for each model.
if (! $errors) {
    foreach ($modelList as $modelName => $modelInfo) {
        echo "ii Generating " . $modelName . "\n";
        $primaryKey  = $modelInfo['primaryKey'];
        $foreignKey  = $modelInfo['foreignKey'];
        $fieldData   = $modelInfo['fieldData'];
        $sources     = $modelInfo['sources'];
        $modelFields = $modelInfo['modelFields'];
        $masterRead  = $modelInfo['masterRead'];

        // generate code for this mapper.
        if (count($primaryKey) == 1) {
            $keys = array_keys($primaryKey);
            $primaryKey = $primaryKey[$keys[0]];
        }

        $testName   = MODEL_NAMESPACE . $modelName . 'Test';
        $mapperName = MAPPER_NAMESPACE . $modelName;
        $modelName  = MODEL_NAMESPACE . $modelName;


        // Generate the mapper
        $newMapper = new Zend_CodeGenerator_Php_Class();
        $newMapper->setName($mapperName);
        $newMapper->setExtendedClass('Insta_Mapper_Abstract');

        $docBlock = new Zend_CodeGenerator_Php_Docblock();
        $docBlock->setShortDescription('Auto Generated Mapper for ' . $mapperName);
        $newMapper->setDocblock($docBlock);

        $construct = new Zend_CodeGenerator_Php_Method();
        $construct->setName('__construct');
        $parameter = new Zend_CodeGenerator_Php_Parameter();
        $parameter->setName('model');
        $construct->setParameter($parameter);
        $construct->setBody("parent::__construct(\$model);\n\$this->_readFromMaster = {$masterRead};");

        $newMapper->setMethod($construct);
        unset($construct);

        // the initializeFields method
        $initFields = new Zend_CodeGenerator_Php_Method();
        $initFields->setName('initializeFields');
        $initFields->setVisibility(Zend_CodeGenerator_Php_Method::VISIBILITY_PROTECTED);

        $docBlock = new Zend_CodeGenerator_Php_Docblock();
        $docBlock->setShortDescription('Initialize fields for ' . $mapperName);
        $initFields->setDocblock($docBlock);

        // build the body . start each with a line break for formatting
        $regFields = "\n";
        $dbCode    = "\$this->_dbFields = array(\n";
        $ucCode    = "\$this->_ucFields = array(\n";
        $fkCode    = "\$this->_fkFields = array(\n";
        $ucPkCode  = "\$this->_ucPrimaryKey = ";
        $stmCode   = "\$this->_stmFieldMap = array(\n";

        foreach ($modelFields as $field) {
            $regFields .= "\$this->_model->registerField('$field');\n";
            switch ($fieldData[$field]['source']) {
                case 'db':
                    $dbCode .= "    '$field' => new Insta_BaseMapper_Field_DbField(
                    '{$fieldData[$field]['sourceField']}', '{$fieldData[$field]['sourceType']}', 
                    '{$fieldData[$field]['modelField']}', '{$fieldData[$field]['modelType']}', 
                    '{$fieldData[$field]['sourceName']}', " . ($fieldData[$field]['primary'] ? 'true' : 'false') . "),\n";
                    break;
                case 'uc':
                    $ucCode .= "    '$field' => new Insta_BaseMapper_Field_UcField(
                    '{$fieldData[$field]['sourceField']}', '{$fieldData[$field]['sourceType']}', 
                    '{$fieldData[$field]['modelField']}', '{$fieldData[$field]['modelType']}', 
                    '{$fieldData[$field]['sourceName']}'),\n";
                    break;
            }
            $stmCode .= "    '{$fieldData[$field]['sourceField']}' => '$field',\n";
        }

        if (is_array($primaryKey)) {
            // build uc pk as array
            $ucPkCode .= "array(\n";
            foreach ($primaryKey as $source => $pk) {
                $ucPkCode .= "'$source' => '$pk',";
            }
            $ucPkCode .= ");";
        } else {
            $ucPkCode .= "'$primaryKey';\n";
        }

        if (is_array($foreignKey)) {
            foreach ($foreignKey as $fm => $fk) {
                $fkCode .= "'$fm' => '{$fk['foreign']}',";
            }
        }
        $dbCode  .= ");\n";
        $ucCode  .= ");\n";
        $fkCode  .= ");\n";
        $stmCode .= ");\n";

        $initFields->setBody($regFields . $dbCode . $ucCode . $fkCode . $ucPkCode . $stmCode);
        unset($regFields, $dbCode, $ucCode, $fkCode, $ucPkCode, $stmCode);

        $newMapper->setMethod($initFields);
        unset($initFields);

        // these are both based on sources
        //initializeReadOperations
        $initRead = new Zend_CodeGenerator_Php_Method();
        $initRead->setName('initializeReadOperations');
        $initRead->setVisibility(Zend_CodeGenerator_Php_Method::VISIBILITY_PROTECTED);
        //initializeTableAdapters
        $initTables = new Zend_CodeGenerator_Php_Method();
        $initTables->setName('initializeTableAdapters');
        $initTables->setVisibility(Zend_CodeGenerator_Php_Method::VISIBILITY_PROTECTED);

        $readBody  = "\$this->_readOperations = array(\n";
        $tableBody = "\$this->_tableAdapters = array(\n";

        foreach ($sources as $source) {
            switch ($source['type']) {
                case 'db':
                    // db goes in read and table body
                    $readBody .= "    'db' => array('{$source['query']}', '{$source['db']}'),\n";
                    $tableBody .= "    '{$source['name']}' => '{$source['db']}',\n";
                    break;
                case 'uc':
                    // uc goes in read
                    $readBody .= "    'uc' => '{$source['query']}',\n";
                    break;
            }
        }

        $readBody  .= ");\n";
        $tableBody .= ");\n";
        $initRead->setBody($readBody);
        $initTables->setBody($tableBody);

        unset($readBody, $tableBody);

        $newMapper->setMethods(array($initRead, $initTables));
        unset($initRead, $initTables);

        // save to file
        $file = new Zend_CodeGenerator_Php_File();
        $file->setClass($newMapper);

        $filename = getFileName($newMapper->getName());

        echo "++ Output: " . $output . $filename . "\n";

        if (! is_dir($output . dirname($filename))) {
            mkdir($output . dirname($filename), 0777, true);
        }
        file_put_contents($output . $filename, $file->generate());

        unset($newMapper);

        // Generate the magical model here.
        $newModel  = new Zend_CodeGenerator_Php_Class();
        $newModel->setName($modelName);
        $newModel->setExtendedClass('Insta_Model_Abstract');

        $docBlock = new Zend_CodeGenerator_Php_Docblock();
        $docBlock->setShortDescription('Auto Generated Model for ' . $modelName);
        $newModel->setDocblock($docBlock);


        $construct = new Zend_CodeGenerator_Php_Method();
        $construct->setName('__construct');
        $construct->setBody("parent::__construct('$primaryKey');\n\$this->setMapper(new $mapperName(\$this));");

        $newModel->setMethod($construct);
        unset($construct);

        $basicTypes = Insta_Mapper_Field_Abstract::$basicTypes;

        foreach ($modelFields as $field) {
            // create a getter and setter for each.
            $setMethod = new Zend_CodeGenerator_Php_Method();
            $getMethod = new Zend_CodeGenerator_Php_Method();

            $setMethod->setName('set' . ucfirst($field));
            $parameter = new Zend_CodeGenerator_Php_Parameter();
            $parameter->setName($field);

            if (in_array($fieldData[$field]['modelType'], $basicTypes)) {
                // enable type cast.
                $cast = '(' . $fieldData[$field]['modelType'] . ')';
            } else {
                $cast = '';
            }

            $docBlock = new Zend_CodeGenerator_Php_Docblock();
            $tag      = new Zend_CodeGenerator_Php_Docblock_Tag_Param(array('paramName' => $field, 'datatype' => $fieldData[$field]['modelType']));
            $docBlock->setTag($tag);
            $docBlock->setShortDescription('Set ' . $fieldData[$field]['sourceField'] . ' to ' . $fieldData[$field]['source']);

            $setMethod->setDocblock($docBlock);
            $setMethod->setParameter($parameter);
            $setMethod->setBody("\$this->setField('$field', $cast \$$field);");

            $getMethod->setName('get' . ucfirst($field));
            $getMethod->setBody("return \$this->_fields['$field'];");
            $docBlock = new Zend_CodeGenerator_Php_Docblock();
            $tag      = new Zend_CodeGenerator_Php_Docblock_Tag_Return(array('datatype' => $fieldData[$field]['modelType']));
            $docBlock->setTag($tag);
            $docBlock->setShortDescription('Returns ' . $fieldData[$field]['sourceField'] . ' from ' . $fieldData[$field]['source']);
            $getMethod->setDocblock($docBlock);

            $newModel->setMethod($setMethod);
            $newModel->setMethod($getMethod);

            unset($setMethod, $getMethod, $docBlock, $tag);
        }

        // write out file
        $file = new Zend_CodeGenerator_Php_File();
        $file->setClass($newModel);
        // echo $file->generate();

        $filename = getFileName($newModel->getName());

        echo "++ Output: " . $output . $filename . "\n";
        if (! is_dir($output . dirname($filename))) {
            mkdir($output . dirname($filename), 0777, true);
        }
        file_put_contents($output . $filename, $file->generate());
        // cleanup
        unset($newModel);


        // XXX generate model unit test


    }
} else {
    echo "Models not generated due to errors.\n";
}

function getFileName($className) {
    $filename  = '';
    $namespace = '';
    if ($lastNsPos = strripos($className, '_')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $filename  = str_replace('_', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $filename .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    return $filename;
}

// generally speaking, this script will be run from the command line
echo "\n** DONE.\n";
return true;
