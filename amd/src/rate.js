define(['jquery', 'core/ajax', 'core/notification'],
function($, ajax, notification) {

    var RateAction = function(selector, courseid) {
        this._region = $(selector);
        this._courseid = courseid;
        this._region.find('.star').unbind().on('click', 'img', this._setUserChoice.bind(this));
    };

    RateAction.prototype._setUserChoice = function(element) {
        var elem = $(element.target).parent();
        var value = elem.data('value');

        if (value != '') {
            ajax.call([{
                methodname: 'block_rate_course_set_rating',
                args: {
                    courseid: this._courseid,
                    rating: value
                },
                done: function(data) {
                    if (data === true) {
                        this._region.find('#block_rate_course-myrating').text(value);
                    }
                    return true;
                }.bind(this),
                fail: notification.exception
            }]);
        }
    };

    return RateAction;
});