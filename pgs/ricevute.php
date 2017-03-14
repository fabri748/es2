<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$msg = '';
$tbl = 'ricevute';
$id = (!empty($_REQUEST['id'])) ? intval($_REQUEST['id']) : false;
$dal = (empty($_POST['dal'])) ? '1970-01-01' : $_POST['dal']; //queste tre righe servono per 
$al = (empty($_POST['al'])) ? date('Y-m-d') : $_POST['al'];   //il filtraggio delle date
$al = date('Y-m-d');            //


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
$data = R::find('ricevute', 'dataemissione BETWEEN "' . $dal . '" AND "' . $al . '" ORDER by id ASC LIMIT 999'); // sostituisco il data qua sotto con questa versione
//$data = R::findAll($tbl, 'ORDER by id ASC LIMIT 999');
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



    <form method="post" action="?p=ricevute"> <!--FORM PER IL FILTRO DATA CONTROLLARE IL CAMPO ACTION CHE PUNTI ALLA PAGINA CORRETTA-->
        <label for="da">
            dal 
        </label>
        <input name="dal" type="date"  value="<?= $dal ?>"   />
        <label for="a">
            al
        </label>
        <input name="al"  type="date" value="<?= $al ?>"   />

        <button type="submit" tabindex="-1">
            Filtra
        </button>

    </form> <!--FINE FORM FILTRO DATA -->

    <div class="tablecontainer">
        <table border="0" cellspacing="5" cellpadding="5">
            <table id="tabella" class="table table-striped table-bordered responsive">
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
<script src="https://code.jquery.com/jquery-3.1.1.js" ></script>

<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js" ></script>



<script>
    $(document).ready(function () {
//DATATABLE
//metto alla variabile otable la mia tabella che ho creato
        $('#tabella').dataTable({ // ASSEGNARE L'ID DELLA TABELLA 
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
                        .column(3) //COLONNA SU CUI ESEGUIRE LA SOMMA (SI PARTE A CONTARE DA 0)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                // Total over this page
                pageTotal = api
                        .column(3, {page: 'current'}) //COLONNA SU CUI ESEGUIRE LA SOMMA (SI PARTE A CONTARE DA 0)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                // Update footer
                $(api.column(3).footer()).html( //COLONNA SU CUI ESEGUIRE LA SOMMA (SI PARTE A CONTARE DA 0)
                        '€' + pageTotal + 'Totale della pagina ( €' + total + ' Totale Generale)'
                        );
            }
        });
    });


</script>
