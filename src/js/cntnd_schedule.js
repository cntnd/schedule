/* cntnd_schedule */
$(document).ready(function() {
    // knockout
    function Team(data) {
        this.name = ko.observable(data.name);
        this.url = ko.observable(data.url);
        this.team = ko.observable(data.team);
        if (data.side===undefined){
            this.side = ko.observable('left');    
        }
        else {
            this.side = ko.observable(data.side);
        }
        this.index = ko.observable(data.index);
    }

    function TeamViewModel() {
        // Data
        var self = this;
        self.newTeamText = ko.observable();
        self.availableTeams = scheduleTeams;

        // Sortable
        self.teamsLeft = ko.observableArray([]);
        self.teamsLeft.id='left';
        self.saveTeamsLeft = function(){ return ko.toJSON(self.teamsLeft); };
        self.teamsRight = ko.observableArray([]);
        self.teamsRight.id='right';
        self.saveTeamsRight = function(){ return ko.toJSON(self.teamsRight); };

        self.myDropCallback = function (arg) {
            arg.item.side(arg.targetParent.id);
            arg.item.index(arg.targetIndex);
            if (console) {
                console.log("Moved '" + arg.item.name() + "' from " + arg.sourceParent.id + " (index: " + arg.sourceIndex + ") to " + arg.targetParent.id + " (index " + arg.targetIndex + ")");
            }
        };

        // Operations
        self.addTeam = function() {
            self.teamsLeft.push(new Team({ name: this.newTeamText() }));
            self.newTeamText('');
        };

        self.removeTeam = function(team) { self.teamsLeft.remove(team) };

        self.resetTeams = function(){
            var mappedTeamsLeft = $.map(teamsLeftJson, function(item) { return new Team(item) });
            self.teamsLeft(mappedTeamsLeft);
            var mappedTeamsRight = $.map(teamsRightJson, function(item) { return new Team(item) });
            self.teamsRight(mappedTeamsRight);
        };
        self.eraseTeams = function(){
            self.teamsLeft([]);
            self.teamsRight([]);
        };

        // Load initial state from server, convert it to Team instances, then populate self.tasks
        console.log(teamsLeftJson, $.isEmptyObject(teamsLeftJson));
        if (teamsLeftJson!==undefined && !$.isEmptyObject(teamsLeftJson)) {
            var mappedTeamsLeft = $.map(teamsLeftJson, function(item) { return new Team(item) });
            self.teamsLeft(mappedTeamsLeft);
        }
        else if (!$.isEmptyObject(self.availableTeams)) {
            var mappedTeamsLeft = $.map(self.availableTeams, function(item) { return new Team(item) });
            self.teamsLeft(mappedTeamsLeft);
        }
        if (teamsRightJson!==undefined) {
            var mappedTeamsRight = $.map(teamsRightJson, function(item) { return new Team(item) });
            self.teamsRight(mappedTeamsRight);
        }
    }

    ko.applyBindings(new TeamViewModel());
});