<?php

namespace Typo3Api\Tca\Field;


class InputFieldTest extends AbstractFieldTest
{
    public function createFieldInstance(string $name, array $options = [], $extendDefaults = true): AbstractField
    {
        if ($extendDefaults) {
            $options = ['dbType' => self::STUB_DB_TYPE] + $options;
        }

        return new InputField($name, $options);
    }

    public function assertBasicCtrlChange(AbstractField $field)
    {
        $ctrl = [];
        $field->modifyCtrl($ctrl, 'stub_table');
        $this->assertEquals([
            'searchFields' => $field->getName(),
            'label' => $field->getName(),
        ], $ctrl);
    }

    protected function assertBasicColumns(AbstractField $field)
    {
        $this->assertEquals([
            $field->getName() => [
                'label' => $field->getOption('label'),
                'config' => [
                    'type' => 'input',
                    'size' => 25,
                    'max' => 50,
                    'eval' => 'trim'
                ]
            ]
        ], $field->getColumns('some_table'));
    }

    public static function differentSizeProvider()
    {
        return [
            [10],
            [50],
            [250],
            [500],
        ];
    }

    /**
     * @dataProvider differentSizeProvider
     * @param int $size
     */
    public function testDifferentSizes(int $size)
    {
        $fieldName = 'test_field_1';
        $field = $this->createFieldInstance($fieldName, ['max' => $size], false);
        $this->assertEquals($size, $field->getColumns('t')[$fieldName]['config']['max']);
        $this->assertEquals($size / 2, $field->getColumns('t')[$fieldName]['config']['size']);
        $this->assertEquals("`$fieldName` VARCHAR($size) DEFAULT '' NOT NULL", $field->getDbTableDefinitions('t')['t'][0]);
    }
}