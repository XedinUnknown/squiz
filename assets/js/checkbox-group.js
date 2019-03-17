/**
 * Allows creation of checkbox groups with limit.
 *
 * - Enclose the checkboxes in a `<fieldset>` with class `checkbox-group`.
 * - Add `data-limit` attribute to it. Value controls how many are allowed to be checked at any time.
 * - Any non-positive value means there's no limit.
 * - When the limit is reached, remaining checkboxes in fieldset will be disabled.
 */
;(function ($) {
    $(function () {
        let selCheckbox = 'input[type="checkbox"]';
        $('fieldset.checkbox-group').on('change', selCheckbox, function (e) {
            let $group = $(e.delegateTarget);
            let limit = parseInt($group.data('limit'));

            if (!limit) {
                return true;
            }

            let $children = $group.find(selCheckbox);
            let $checked = $children.filter('input:checked');
            let $unchecked = $children.filter('input:not(:checked)');

            // Un-disable all
            $children.attr('disabled', false);

            // If over limit, uncheck back
            if ($checked.size() > limit) {
                $(this).attr('checked', false);
            }

            // If limit reached, disable the rest
            if ($checked.size() === limit) {
                $unchecked.attr('disabled', true);
            }
        });
    });
}(jQuery));
