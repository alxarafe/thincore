/* Copyright 2017-2018 Rafael San José Tovar (http://www.rsanjoseo.com) */

var puedoCerrar = true;

function permitirCerrar() {
    puedoCerrar = true;
}

function fieldChanged() {
    puedoCerrar = false;
}

function iCanClose() {
    alert(puedoCerrar);
    if (!puedoCerrar) {
        return 'Para evitar perder cambios por error, se recomienda usar los botones Guardar o Salir';
    }
}

function showDetails(selector) {
    $("." + selector.id).each(function (index) {
        if ($(this).css('display') == 'none') {
            $(this).css('display', '');
        } else {
            $(this).css('display', 'none');
        }
    })
}

function addLine(theLine) {
    table = document.getElementById("the_table");
    i = table.rows.length;
    row = table.insertRow();
    row.id = "fila" + i;
    row.className = "success";
    row.innerHTML = theLine.replace(/`/g, "'").replace(/#/g, i);
    sp = row.getElementsByClassName('selectpicker');
    for (var i = 0; i < sp.length; i++) {
        $("#" + sp[i].id).selectpicker();
    }
    return false;
}


function addusr() {
    table = document.getElementById("the_table");
    i = table.rows.length;
    row = table.insertRow();
    row.id = "fila" + i;
    row.className = "success";
    row.innerHTML =
        '<td><input id="id' + i + '" name="id[' + i + ']" value="' + i + '" hidden />' + i + '</td>' +
        '<td>user' + i + '</td>' +
        '<td><input id="admin' + i + '" type="checkbox" name="admin[' + i + ']" value="0"> Administrador</input></td>' +
        '<td><input id="active' + i + '" type="checkbox" name="active[' + i + ']" value="1" onClick="checkactive(0);" checked> Activo</input>';
    return false;
}

function addsec() {
    table = document.getElementById("the_table");
    i = table.rows.length;
    row = table.insertRow();
    row.id = "fila" + i;
    row.className = "success";
    row.innerHTML =
        '<td><input id="id' + i + '" name="id[' + i + ']" value="' + i + '" hidden />' + i + '</td>' +
        '<td><input id="nombre' + i + '" type="text" name="nombre[' + i + ']" value=""></input></td>' +
        '<td><input id="active' + i + '" type="checkbox" name="active[' + i + ']" value="1" onClick="checkactive(0);" checked> Activo</input>';
    return false;
}

function newDataTable(name, paging = 0) {
    $('#' + name).DataTable({
        'pageLength': paging,
        'lengthChange': false,
        'searching': true,
        'ordering': true,
        'info': true,
        'autoWidth': false,
        "language": {
            "lengthMenu": "Mostrando _MENU_ registros por página",
            "zeroRecords": "No hay datos",
            "info": "Mostrando página _PAGE_ de _PAGES_",
            "infoEmpty": "No hay datos disponibles",
            "infoFiltered": "(Encontrados _TOTAL_ de _MAX_)",
            "search": "Buscar:",
            "infoPrevious": "Anterior",
            "paginate": {
                "first": "Primera",
                "previous": "Anterior",
                "next": "Siguiente",
                "last": "Última",
            },
        }
    });
}

function selectPage(code, page, pages, itemsxpage) {
    $("#" + code + "-first").attr('class', page != 1 ? "enabled" : "disabled");
    $("#" + code + "-last").attr('class', page != pages ? "enabled" : "disabled");
    for (i = 1; i <= pages; i++) {
        $("#" + code + "-" + i).attr('class', ((page != i) ? "enabled" : "active"));
        $("." + code + "p" + i).each(function (index) {
            $(this).css('display', page == i ? '' : 'none');
        });
    }
    return false;
}

$(document).ready(function ($) {
    if (typeof (tables) == 'object') {
        for (var table in tables) {
            newDataTable(table, tables[table]);
        }
    }
});