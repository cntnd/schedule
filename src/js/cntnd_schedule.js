/* cntnd_schedule */
$(document).ready(function() {
    // knockout
    function Team(data) {
        this.name = ko.observable(data.name);
        this.url = ko.observable(data.url);
        this.team = ko.observable(data.team);
    }

    function TeamViewModel() {
        // Data
        var self = this;
        self.teams = ko.observableArray([]);
        self.newTeamText = ko.observable();
        self.availableTeams = scheduleTeams;
        self.loadTeams = teamJson;

        // Operations
        self.addTeam = function() {
            self.teams.push(new Team({ name: this.newTeamText() }));
            self.newTeamText("");
        };
        self.removeTeam = function(team) { self.teams.remove(team) };
        self.saveTeams = function(){ return ko.toJSON(self.teams); };

        self.resetTeams = function(){
            var mappedTeams = $.map(self.loadTeams, function(item) { return new Team(item) });
            self.teams(mappedTeams);
        };
        self.eraseTeams = function(){
            self.teams([]);
        };

        // Load initial state from server, convert it to Team instances, then populate self.tasks
        if (self.loadTeams!==undefined) {
            var mappedTeams = $.map(self.loadTeams, function(item) { return new Team(item) });
            self.teams(mappedTeams);
        }
    }

    ko.applyBindings(new TeamViewModel());

    // sortable
    function getOrder(object) {
        var dataList = $(object).map(function () {
            return $(this).data("id");
        }).get();

        return dataList.join("|");
    }
    function saveOrder(){
        $('#orderLeft').val(getOrder("#sortable-left .list-group-item"));
        $('#orderRight').val(getOrder("#sortable-right .list-group-item"));
    }

    var listLeft = document.getElementById('sortable-left');
    var listRight = document.getElementById('sortable-right');

    Sortable.create(listLeft, {
        group: 'list',
        animation: 100,
        onEnd: saveOrder
    });
    Sortable.create(listRight, {
        group: 'list',
        animation: 100,
        onEnd:  saveOrder
    });
});