<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$msg = '';
$tbl = 'ricevute';
$id = (!empty($_REQUEST['id'])) ? intval($_REQUEST['id']) : false;
$record = (empty($_REQUEST['id'])) ? R::dispense($tbl) : R::load($tbl, intval($_REQUEST['id']));
if (!empty($_POST['clienti_id'])) :
    foreach ($_POST as $key => $value) {
        $record[$key] = $value;
    }
    try {
        R::store($record);
        $msg = 'Dati salvati correttamente (' . json_encode($record) . ') ';
    } catch (RedBeanPHP\RedException\SQL $e) {
        $msg = $e->getMessage();
    }
endif;

if (!empty($_REQUEST['del'])) :
    $record = R::load($tbl, intval($_REQUEST['del']));
    try {
        R::trash($record);
    } catch (RedBeanPHP\RedException\SQL $e) {
        $msg = $e->getMessage();
    }
endif;

$data = R::findAll($tbl, 'ORDER by id ASC LIMIT 999');
$clienti = R::findAll('clienti');
$new = !empty($_REQUEST['create']);
?>

<h1>
    <a href="index.php">
        <?= ($id) ? ($new) ? 'Nuova ricevuta' : 'Ricevuta n. ' . $id : 'Ricevute'; ?>
    </a>
</h1>
<?php if ($id || $new) : ?>
    <form method="post" action="?p=<?= $tbl ?>">
        <?php if ($id) : ?>
            <input type="hidden" name="id" value="<?= $record->id ?>" />
        <?php endif; ?>

        <label for="dataemissione">
            Data
        </label>
        <input name="dataemissione"  value="<?= date('Y-m-d', strtotime($record->dataemissione)) ?>" type="date" />

        <label for="clienti_id">
            Cliente
        </label>
        <select name="clienti_id">
            <option />
            <?php foreach ($clienti as $opt) : ?>
                <option value="<?= $opt->id ?>" <?= ($opt->id == $id) ? 'selected' : '' ?> >
                    <?= $opt->nome ?>
                </option>
            <?php endforeach; ?>
        </select>
        <label for="descrizione">
            Descrizione
        </label>
        <input name="descrizione"  value="<?= $record->descrizione ?>" autofocus required  />			
        <label for="importo">
            Importo
        </label>			
        <input name="importo" value="<?= $record->importo ?>" type="number" step="any" />
        <button type="submit" tabindex="-1">
            Salva
        </button>

        <a href="?p=<?= $tbl ?>" >
            Elenco
        </a>			

        <a href="?p=<?= $tbl ?>&del=<?= $ma['id'] ?>" tabindex="-1">
            Elimina
        </a>					
    </form>
<?php else : ?>


	<div id="baseDateControl">
 <div class="dateControlBlock">
        Between <input type="text" name="dateStart" id="dateStart" class="datepicker"   /> and 
        <input type="text" name="dateEnd" id="dateEnd" class="datepicker" />
    </div>
</div>

    <div class="tablecontainer">
        <table border="0" cellspacing="5" cellpadding="5">
            
        </br>

        <table style="table-layout:fixed" id="example" class="display table table-row table-bordered responsive" cellspacing="0" width="100%">
            <colgroup>
                <col style="width:250px" />
            </colgroup>
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Descrizione</th>
                    <th>Importo</th>
                    <th>Email</th>
                    <th>Telefono</th>
                    <th>Cellulare</th>
                    <th style="width:100px;text-align:center">Modifica</th>
                    <th style="width:100px;text-align:center">Cancella</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align:right">Total:</th>
                    <th></th>
                </tr>
            </tfoot>
            <tbody>
                <?php foreach ($data as $r) : ?>
                    <tr>
                        <td>
                            <?= ($r->clienti_id) ? $r->clienti->nome : '' ?>
                        </td>			
                        <td>
                            <?= date('d/m/Y', strtotime($r->dataemissione)) ?>
                        </td>
                        <td>
                            <?= $r->descrizione ?>
                        </td>
                        <td style="text-align:right" >
                            <?= sprintf("%.2f", $r->importo) ?>
                        </td>
                        <td>
                            <?= ($r->clienti_id) ? $r->clienti->email : '' ?>
                        </td>
                        <td>
                            <?= ($r->clienti_id) ? $r->clienti->telefono : '' ?>
                        </td>	
                        <td>
                            <?= ($r->clienti_id) ? $r->clienti->cellulare : '' ?>
                        </td>	
                        <td style="text-align:center" >
                            <a href="?p=<?= $tbl ?>&id=<?= $r['id'] ?>">
                                Mod.
                            </a>
                        </td>
                        <td style="text-align:center" >
                            <a href="?p=<?= $tbl ?>&del=<?= $r['id'] ?>" tabindex="-1">
                                x
                            </a>
                        </td>							
                    </tr>		
                <?php endforeach; ?>
            </tbody>
        </table>
        <h4 class="msg">
            <?= $msg ?>
        </h4>	
    </div>
<?php endif; ?>
<a href="?p=<?= $tbl ?>&create=1">Inserisci nuovo</a>
<script>
    var chg = function (e) {
        console.log(e.name, e.value)
        document.forms.frm.elements[e.name].value = (e.value) ? e.value : null
    }
</script>
<script src="https://code.jquery.com/jquery-3.1.1.js" integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA=" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js" ></script>
<script src="https://cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js" ></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.13/filtering/row-based/range_dates.js" ></script>

<script>


    //SOMMATORIA DEI RISULTATI MOSTRATI A VIDEO E DEI TOTALI A FIANCO

    $(document).ready(function () {
        $('#example').DataTable({
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function (i) {
                    return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                };

                // Total over all pages
                total = api
                        .column(3) //  <-- QUA CI VA IL NUMERO DELLA COLONNA PARTENDO A CONTARE DA 0 (nel nostro caso l'importo)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                // Total over this page
                pageTotal = api
                        .column(3, {page: 'current'})  //  <-- QUA CI VA IL NUMERO DELLA COLONNA PARTENDO A CONTARE DA 0 (nel nostro caso l'importo)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                // Update footer
                $(api.column(3).footer()).html(
                        '€' + pageTotal + 'Totale della pagina ( €' + total + ' Totale Generale)'
                        );
            }
        });

		//FILTRO TABELLA PER DATA
		
		$( "#dateStart" ).datepicker({
  dateFormat: "dd/mm/yy"
});
$( "#dateEnd" ).datepicker({
  dateFormat: "dd/mm/yy"
});
		
				
$.fn.dataTableExt.afnFiltering.push(
		function(oSettings, aData, iDataIndex){
			var dateStart = parseDateValue($("#dateStart").val());
			var dateEnd = parseDateValue($("#dateEnd").val());
			// aData represents the table structure as an array of columns, so the script access the date value 
			// in the first column of the table via aData[0]
			var evalDate= parseDateValue(aData[1]); //  <-- QUA CI VA IL NUMERO DELLA COLONNA PARTENDO A CONTARE DA 0 (nel nostro caso la data)
			
			if (evalDate >= dateStart && evalDate <= dateEnd) {
				return true;
			}
			else {
				return false;
			}
			
		});

// Function for converting a mm/dd/yyyy date value into a numeric string for comparison (example 08/12/2010 becomes 20100812
function parseDateValue(rawDate) {
	var dateArray= rawDate.split("/");
	var parsedDate= dateArray[2] + dateArray[0] + dateArray[1];
	return parsedDate;
}


$(function() {
	// Implements the dataTables plugin on the HTML table
	var $dTable= $("#example").dataTable();  //   <-	QUA CI VA L'ID DELLA TABELLA
	
	// Create event listeners that will filter the table whenever the user types in either date range box or
	// changes the value of either box using the Datepicker pop-up calendar
	$("#dateStart").keyup ( function() { $dTable.fnDraw(); } );
	$("#dateStart").change( function() { $dTable.fnDraw(); } );
	$("#dateEnd").keyup ( function() { $dTable.fnDraw(); } );
	$("#dateEnd").change( function() { $dTable.fnDraw(); } );

});
		
       
    });

</script>
