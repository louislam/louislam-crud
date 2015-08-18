/**
 * Created by Louis Lam on 8/15/2015.
 */
var LouisCRUD = (function () {
    function LouisCRUD() {
        var self = this;
        $(document).ready(function () {
            self.table = $('#table').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
            $('#table tfoot th').each(function () {
                var title = $('#table thead th').eq($(this).index()).text();
                $(this).html('<input type="text" placeholder="Search ' + title + '" />');
            });
            // Apply the search
            self.table.columns().every(function () {
                var that = this;
                $('input', this.footer()).on('keyup change', function () {
                    that.search(this.value).draw();
                });
            });
            // Delete Button
            $(".btn-delete").click(function () {
                var result = window.confirm("Are you sure?");
                if (result) {
                    var btn = $(this);
                    var deleteLink = $(this).data("url");
                    $.ajax({
                        url: deleteLink,
                        type: "DELETE"
                    }).done(function (data) {
                        $("#row-" + btn.data("id")).remove();
                    }).fail(function (data) {
                        console.log(data);
                    });
                }
            });
            // Ajax Form
            $("form.ajax").submit(function () {
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
            self.ckEditor();
        });
    }
    // CKEditor
    LouisCRUD.prototype.ckEditor = function () {
    };
    LouisCRUD.prototype.setAjaxFormCallback = function (callback) {
        this.ajaxFormCallback = callback;
    };
    return LouisCRUD;
})();
//# sourceMappingURL=LouisCRUD.js.map