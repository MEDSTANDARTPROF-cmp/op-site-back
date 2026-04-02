<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Settings;
use PhpOffice\PhpSpreadsheet\Shared\File;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Collection\CellsFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MsIeXLSX2Writer extends MsIeWriter
{
    /** @var Spreadsheet $writer */
    public $writer;

    /**
     * @param array $config
     * @return bool
     */
    public function initialize(array $config = array())
    {
        if ($ok = parent::initialize($config)) {
            try {
                $this->writer = new Spreadsheet();
                $this->writer->getDefaultStyle()->getFont()->setName('Arial');
                $this->writer->getDefaultStyle()->getFont()->setSize(10);
                $this->writer->getDefaultStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $this->writer->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                $this->writer->setActiveSheetIndex(0);
                $this->setSheetName('export');
                File::setUseUploadTempDirectory(true);
            } catch (Exception $e) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
                return false;
            }
        }
        return $ok;
    }

    /**
     * @param array $data
     * @param array $options
     * @return Worksheet|null
     */
    public function write(array $data, array $options = array())
    {
        if ($sheet = $this->getSheet()) {
            $options = array_merge(
                array(
                    'mergeCells' => array('columnIndex1' => 0, 'row1' => 0, 'columnIndex2' => null, 'row2' => null),
                    'style' => array(),
                    'attach' => array(),
                    'autoSize' => $this->modx->getOption('excel_column_auto_size', $options, 0, true),
                ),
                $options
            );
            $mergeCells = $options['mergeCells'];

            try {
                $idx = 1;
                $this->offset++;
                foreach ($data as $k => $v) {
                    if ($v !== null) {
                        if (preg_match('/^attach_.*/', $k) && file_exists($v)) {
                            $attachSettings = $options['attach_settings'] ?: array();
                            $this->attach($v, $idx, $this->offset, $attachSettings);
                        } else {
                            $sheet->setCellValueByColumnAndRow($idx, $this->offset, $v);
                        }
                        if (!empty($options['style'])) {
                            $sheet->getStyleByColumnAndRow($idx, $this->offset)->applyFromArray($options['style']);
                        }
                    }
                    if (!empty($mergeCells['columnIndex1'])) {
                        if (empty($mergeCells['row1'])) {
                            $mergeCells['row1'] = $this->offset;

                        }
                        if (empty($mergeCells['row2'])) {
                            $mergeCells['row2'] = $this->offset;
                        }
                        $sheet->mergeCellsByColumnAndRow(
                            $mergeCells['columnIndex1'],
                            $mergeCells['row1'],
                            $mergeCells['columnIndex2'],
                            $mergeCells['row2']
                        );
                    }
                    if ($options['autoSize']) {
                        $sheet->getColumnDimensionByColumn($k)->setAutoSize(true);
                    }
                    $idx++;
                }
                return $sheet;
            } catch (Exception $e) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
                return null;
            }

        }
        return null;
    }

    /**
     * @param $path
     * @param int $cell
     * @param int $row
     * @param array $options
     * @return bool
     */
    public function attach($path, $cell, $row, array $options = array())
    {
        if ($sheet = $this->getSheet()) {
            try {
                $coordinates = $sheet->getCellByColumnAndRow($cell, $row)->getCoordinate();
                $drawing = new Drawing();
                $drawing->setPath($path);
                $drawing->setCoordinates($coordinates);
                $drawing->setResizeProportional(true);
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                if (!empty($options['width'])) {
                    $drawing->setWidth($options['width']);
                }
                if (!empty($options['height'])) {
                    $drawing->setHeight($options['height']);
                }
                $sheet->getColumnDimensionByColumn($cell)->setWidth(($drawing->getWidth() + 20) * 0.125);
                $sheet->getRowDimension($row)->setRowHeight(($drawing->getHeight() + 10) - ($drawing->getHeight() * 0.25));
                $drawing->setWorksheet($sheet);
                unset($sheet);
                unset($drawing);
            } catch (Exception $e) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * @param string $path
     * @param array $options
     * @return bool
     */
    public function save($path, array $options = array())
    {
        try {
            $path = $this->preparePath($path);
            $writer = new Xlsx($this->writer);
            $writer->save($path);
            unset($writer);
            $this->close();
            return true;
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
            return false;
        }
    }

    public function close()
    {
        if ($this->writer) {
            $this->writer->disconnectWorksheets();
            unset($this->writer);
        }
    }

    /**
     * @param int $offset
     */
    public function setOffset($offset)
    {
        try {
            if ($sheet = $this->getSheet()) {
                $iterator = $sheet->getRowIterator();
                $iterator->resetStart($offset);
                $this->offset = $offset;
            }

        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
        }
    }

    /**
     * @return Worksheet
     */
    public function getSheet()
    {
        try {
            return $this->writer->getActiveSheet();
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, "[" . self::class . "]  {$e->getMessage()} Error:\n" . print_r($e, 1));
            return null;
        }

    }

    /**
     * @param string $name
     */
    public function setSheetName($name)
    {
        if ($sheet = $this->getSheet()) {
            $sheet->setTitle($name);
        }
    }


    /**
     * @return string
     */
    public function getType()
    {
        return MsIeTools::FILE_TYPE_XLSX;
    }
}
