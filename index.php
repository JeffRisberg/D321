<!DOCTYPE html>
<html>
<head>
  <title>D3 21</title>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <script type="text/javascript" src="js/d3.js"></script>
  <script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
</head>
<body>

<style>
text {
  font: 10px sans-serif;
}
</style>

<?php
// jSON URL which should be requested
$json_url = 'http://demo.rallyon.com:8080/master/api/challenge/2333/challengeMember?apiKey=94025&max=1000&sort=score&order=desc';

$username = 'your_username';  // authentication
$password = 'your_password';  // authentication
 
// jSON String for request
$json_string = '';
 
// Initializing curl
$ch = curl_init( $json_url );
 
// Configuring curl options
$options = array(
CURLOPT_RETURNTRANSFER => true,
CURLOPT_USERPWD => $username . ":" . $password,   // authentication
CURLOPT_HTTPHEADER => array('Content-type: application/json') ,
CURLOPT_POSTFIELDS => $json_string
);
 
// Setting curl options
curl_setopt_array( $ch, $options );
curl_setopt($ch, CURLOPT_POST, FALSE);

// Getting results
$result = curl_exec($ch); // Getting JSON result string
curl_close($ch);

$array = json_decode($result, true);
?>

<script type="text/javascript"> 
$(document).ready(function() {

  var diameter = 600,
    format = d3.format(",d"),
    color = d3.scale.category20c();

  var svg = d3.select("body").append("svg")
    .attr("width", diameter)
    .attr("height", diameter)
    .attr("class", "bubble");

  var bubble = d3.layout.pack()
    .sort(null)
    .size([diameter, diameter])    
    .padding(5.0);

  var memberList = [
<?php
  foreach ($array as $member) { 
  	if ($member['score'] > 0) {
?>
  {"name": "<?php echo $member['name'] ?>",
	  <?php if (array_key_exists('teamName', $member)) { ?>
	    "teamName": "<?php echo $member['teamName'] ?>", 
	  <?php } ?>
	 "rank": <?php echo $member['rank'] ?>, 
	 "score": <?php echo $member['score'] ?>},
<?php 
    }
  }
?>
  ];
  
  //console.log(memberList);
      
  memberList.sort(function(a,b) { return a.rank - b.rank } );

  var p = d3.scale.category10();
  
	var node = svg.selectAll(".node")
	  .data(bubble.nodes(teamUp(memberList)))	      
	  .enter().append("g")
	    .attr("class", "node")
	    .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });

	node.append("circle")
	    .attr("r", function(d) { return d.r; })
	    .style("fill", function(d) { return d.color; })
	    .style("stroke", function(d) { return d.stroke; });

	node.append("text")
	    .attr("dy", ".3em")
	    .style("text-anchor", "middle")
	    .attr("transform", function(d) { return "translate(" + 0 + "," + (d.color == "white" ? -d.r : 0) + ")"; })
	    .text(function(d, i) { return d.name; });

  d3.select(self.frameElement).style("height", diameter + "px");

  //Group the players by their team
  function teamUp(memberList) {
    var teams = [];

    memberList.forEach(function(node) { 	
     node.teamName = "";
	   if (node.teamName != null) {
		 
		   var team = null;
		   teams.forEach(function(t) {
			   if (t.name == node.teamName) {
				   team = t;
			   }
		   });
		   if (team == null) {
			  team = {name: node.teamName, color: "white", stroke: "black", children: [], "childColor": p(teams.length)};
			  teams.push(team);
		   }
		  
	     team.children.push({name: node.name, value: node.score, color: p(node.score)});
	   }
    });

    return {children: teams, color: "white", stroke: "white"};
  }
});
</script>
</body>
</html>