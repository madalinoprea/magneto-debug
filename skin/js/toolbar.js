
SheepDebug = (function () {


    /**
     * if cookie is set to 1, persistance is disabled
     *
     * @param cookieName
     * @param menuItemId
     * @constructor
     */
    var PersistCookie = function (cookieName, menuItemId) {
        this.cookieName = cookieName;
        this.menuItem = document.getElementById(menuItemId);

        console.log('Is persistance disabled? ' + this.isPersistenceDisabled());
    };

    PersistCookie.prototype = {

        _setCookie: function (name, value) {
            Mage.Cookies.set(name, value);
        },

        _getCookie: function (name) {
            return Mage.Cookies.get(name);
        },

        isPersistenceDisabled: function () {
            return this._getCookie(this.cookieName) == '1';
        },

        enablePersistence: function () {
            this._setCookie(this.cookieName, 0);
            this.menuItem.innerHTML = 'Disable';
        },

        disablePersistence: function () {
            this._setCookie(this.cookieName, 1);
            this.menuItem.innerHTML = 'Enable';
        },

        togglePersistence: function () {
            if (this.isPersistenceDisabled()) {
                this.enablePersistence();
            } else {
                this.disablePersistence();
            }
        }
    };

    return {
        PersistCookie: PersistCookie
    };
})();