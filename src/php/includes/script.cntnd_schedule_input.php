<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/base-64@1.0.0/base64.min.js"></script>
<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.2/knockout-min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js" integrity="sha256-/GKyJ0BQJD8c8UYgf7ziBrs/QgcikS7Fv/SaArgBcEI=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.6.1/Sortable.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/knockout-sortable@1.2.0/build/knockout-sortable.min.js"></script>
<script language="javascript" type="text/javascript">
    $(document).ready(function() {
        // knockout
        function Team(data) {
            this.name = ko.observable(data.name);
            this.url = ko.observable(data.url);
            this.team = ko.observable(data.team);
            this.firstTeam = ko.observable(data.firstTeam);
            this.customTeam = ko.observable(data.customTeam);
            this.homeOnly = ko.observable(data.homeOnly);
            if (data.side===undefined){
                this.side = ko.observable('one');
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
            self.blockOne = ko.observableArray([]);
            self.blockOne.id='one';
            self.saveBlockOne = function(){ return ko.toJSON(self.blockOne); };

            self.blockTwo = ko.observableArray([]);
            self.blockTwo.id='two';
            self.saveBlockTwo = function(){ return ko.toJSON(self.blockTwo); };

            self.blockThree = ko.observableArray([]);
            self.blockThree.id='three';
            self.saveBlockThree = function(){ return ko.toJSON(self.blockThree); };

            self.myDropCallback = function (arg) {
                arg.item.side(arg.targetParent.id);
                arg.item.index(arg.targetIndex);
            };

            // Operations
            self.addTeam = function() {
                self.blockOne.push(new Team({ name: this.newTeamText() }));
                self.newTeamText('');
            };

            self.removeTeamOne = function(team) {
                self.blockOne.remove(team)
            };

            self.removeTeamTwo = function(team) {
                self.blockTwo.remove(team)
            };

            self.removeTeamThree = function(team) {
                self.blockThree.remove(team)
            };

            self.resetTeams = function(){
                var mappedTeamsOne = $.map(teamsBlockOne, function(item) { return new Team(item) });
                self.blockOne(mappedTeamsOne);
                var mappedTeamsTwo = $.map(teamsBlockTwo, function(item) { return new Team(item) });
                self.blockTwo(mappedTeamsTwo);
                var mappedTeamsThree = $.map(teamsBlockThree, function(item) { return new Team(item) });
                self.blockThree(mappedTeamsThree);
            };
            self.eraseTeams = function(){
                self.blockOne([]);
                self.blockTwo([]);
                self.blockThree([]);
            };

            if (teamsBlockOne!==undefined && !$.isEmptyObject(teamsBlockOne)) {
                var mappedTeams = $.map(teamsBlockOne, function(item) { return new Team(item) });
                self.blockOne(mappedTeams);
            }
            else if (!$.isEmptyObject(self.availableTeams)) {
                var mappedTeams = $.map(self.availableTeams, function(item) { return new Team(item) });
                self.blockOne(mappedTeams);
            }
            if (teamsBlockTwo!==undefined) {
                var mappedTeams = $.map(teamsBlockTwo, function(item) { return new Team(item) });
                self.blockTwo(mappedTeams);
            }
            if (teamsBlockThree!==undefined) {
                var mappedTeams = $.map(teamsBlockThree, function(item) { return new Team(item) });
                self.blockThree(mappedTeams);
            }
        }

        ko.applyBindings(new TeamViewModel());
    });
</script>
