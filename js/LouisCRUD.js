/**
 * Created by Louis Lam on 8/15/2015.
 */
var LouisCRUD = (function () {
    function LouisCRUD() {
        var self = this;
        $(document).ready(function () {
            self.table = $('#table').DataTable();
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
                }).done(function (data) {
                    alert(data);
                });
                return false;
            });
        });
    }
    return LouisCRUD;
})();
//# sourceMappingURL=LouisCRUD.js.map