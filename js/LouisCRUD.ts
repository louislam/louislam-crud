/**
 * Created by Louis Lam on 8/15/2015.
 *
 */
/// <reference path="jquery.d.ts" />
/// <reference path="jquery.dataTables-1.9.4.d.ts" />

window.alert2 = function(msg) {
    sweetAlert(msg);
};

window.alertError = function(msg) {
    swal("Error!", msg, "error");
};

class LouisCRUD {

    private table;

    private ajaxFormCallback;

    private validateFunctions = [];

    private errorMsgs = [];

    private isUploading : boolean = false;

    public setUploading(val : boolean) : void {
        this.isUploading = val;
    }

    constructor() {
        var self = this;

        $(document).ready(function () {

            // Init Select2 !
            $(".select2").select2();

            // To style only <select>s with the selectpicker class
            $('.selectpicker').selectpicker();

            // Disable Datatables' alert!
            $.fn.dataTableExt.sErrMode = 'throw';

            // Ajax Submit Form
            $("form.ajax").submit(function (e) {

                e.preventDefault();

                if (self.isUploading) {
                    alert2("Uploading image(s), please wait.");
                    return;
                }

                // Clear all msgs
                self.errorMsgs = [];

                let ok = true;

                let data = {};
                let serialArray = $("#louis-form").serializeArray();

                $.each(serialArray, function () {
                    data[this.name] = this.value;
                });

                // Validate
                for (let i = 0; i < self.validateFunctions.length; i++) {
                    if (self.validateFunctions[i](data) === false) {
                        ok = false;
                    }
                }

               if (!ok) {
                   let str = "";
                   for (let i = 0; i < self.errorMsgs.length; i++) {
                        str += self.errorMsgs[i]  +"\n";
                   }
                   alertError(str);
                   return false;
               }

                // Create Form Data from the form.
               // if ($(this).attr("enctype") !== "undefined") {
                    //data = new FormData($(this)[0]);


                $.ajax({
                    url: $(this).attr("action"),
                    type: $(this).data("method"),
                    data: $(this).serialize()
                }).done(function (result) {
                    if (self.ajaxFormCallback != null) {
                        self.ajaxFormCallback(result);
                    }
                });
                return false;
            });

            // Active Menu Item
            $(".main-sidebar ul li").each(function () {
                if (location.pathname.indexOf($(this).find("a").attr("href")) >= 0) {
                    $(this).addClass("active");
                }
            });

            self.refresh();
        });
    }


    public addValidator(func) {
        this.validateFunctions.push(func);
    }

    public addErrorMsg(msg) {
        this.errorMsgs.push(msg);
    }

    public getDataTable() {
        return this.table;
    }

    /**
     *
     * @param isAjax
     * @param tableURL
     * @param enableSearch
     * @param enableSorting
     */
    public initListView(isAjax : boolean, tableURL : string, enableSearch : boolean = true , enableSorting : boolean = true) {
        let self = this;

        let data = {
            "pageLength": 25,
            "paging": true,
            "ordering": enableSorting,
            "autoWidth": false,
            "searching": enableSearch,
            "info": true,
            "drawCallback": function( settings ) {
              self.refresh();
            },
            "bStateSave": true,
            "fnStateSave": function (oSettings, oData) {
                localStorage.setItem( 'DataTables_'+window.location.pathname, JSON.stringify(oData) );
            },
            "fnStateLoad": function (oSettings) {
                return JSON.parse( localStorage.getItem('DataTables_'+window.location.pathname) );
            }
        };

        if (isAjax) {
            data.serverSide = true;
            data.processing =  true;
            //data.searching = true;
            data.ajax = {
                url: tableURL,
                type: "POST"
            }

        }

        $(document).ready(function () {
            self.table = $('#louis-crud-table').DataTable(data);

            // Column Filter
            self.columnFilter();
        });
    }

    public columnFilter() {
        let self = this;

        $(".column-filter a").click(function (e) {
            e.stopPropagation();
        });

        $(".column-filter [type=checkbox]").change(function (e) {
            e.preventDefault();

            let checked = $(this).is(":checked");

            let column = self.table.column($(this).data('column'));
            column.visible(checked);
        });
    }

    public setAjaxFormCallback(callback) {
        this.ajaxFormCallback = callback;
    }

    public refresh() {
        // Delete Button
        $(".btn-delete:not(.ok)").click(function () {
            let result = window.confirm("Are you sure?");

            if (result) {
                let btn = $(this);
                let deleteLink = $(this).data("url");

                $.ajax({
                    url: deleteLink,
                    type: "DELETE"
                }).done(function (data) {
                    btn.parents('tr').remove();
                   // self.table.ajax.reload();
                }).fail(function (data) {
                    console.log(data);
                });
            }
        }).addClass("ok");

        // Confirm Button
        $(".btn-confirm").click(function (e) {
            e.preventDefault();

            let result = window.confirm($(this).data("msg"));

            if (result) {
                location.href = $(this).attr("href");
            }
        });

    }

    public field(name) {
        return $("#field-" + name);
    }
}

