<?php

namespace util;

use PHPExcel_IOFactory;
use PHPExcel_Style_Fill;

class ReportHelper {
	
	public static function download_excel_file($data, $data_headers, $totals, $title, $dates) {
		
		$letters = range('A', 'Z');
		
		$cells_header = $title . ' for ' . $dates;
		
		$objPHPExcel = new \PHPExcel();
		
		$objPHPExcel->getProperties()->setCreator("nginad.com")
		->setLastModifiedBy("nginad.com")
		->setTitle("Statistic reports - " . $title)
		->setSubject("")
		->setDescription("")
		->setKeywords("")
		->setCategory("");
		
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getSheet(0)->setTitle($title);
		$objPHPExcel->getActiveSheet()->SetCellValue('A1', $cells_header);
		$objPHPExcel->getActiveSheet()->mergeCells('A1:' . $letters[count($data_headers) - 1] . '1');
		
		for ($j = 0; $j < count($data_headers); $j++):
			$objPHPExcel->getActiveSheet()->SetCellValue($letters[$j] . '3', $data_headers[$j]);
		endfor;
		
		$objPHPExcel->getActiveSheet()->getStyle('A3:' . $letters[count($data_headers) - 1] . '3')->getFill() 
			->setFillType(PHPExcel_Style_Fill::FILL_SOLID) 
			->getStartColor()->setARGB('FFDDDDDD');
		
		if (empty($data)):
			$objPHPExcel->getActiveSheet()->SetCellValue('A5', 'No records');
			$objPHPExcel->getActiveSheet()->mergeCells('A5:' . $letters[count($data_headers) - 1] . '5');
		else:
			$i = 4;
			foreach ($data as $elem):
		
				$elem = array_values((array) ($elem));
				for ($j = 0; $j < count($elem); $j++):
					$objPHPExcel->getActiveSheet()->SetCellValue($letters[$j] . $i, $elem[$j]);
				endfor;
		
				$i++;
			endforeach;
			$i++;
			$j = 0;
			foreach ($totals as $key => $elem):
				$objPHPExcel->getActiveSheet()->SetCellValue($letters[$j++] . $i, $totals[$key]);
			endforeach;
		endif;
		
		foreach ($letters as $columnID):
			$objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		endforeach;
		
		$fname = str_replace(" ", "_", $cells_header);
		
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="' . $fname . '"');
		header('Cache-Control: max-age=0');
		
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		
	}	
}
