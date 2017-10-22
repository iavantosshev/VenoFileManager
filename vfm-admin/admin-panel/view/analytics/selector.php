<?php
$getday = false;
$day = false;
$range = false;
if (isset($_GET['range'])) {
    $range = $_GET['range'];
} elseif (isset($_GET['day']) && strlen($_GET['day'] > 0)) {
    $day = $_GET['day'];
    $logs = array($_GET['day'].".json");
    $getday = true;
} else {
    $range = 1;
}
$loglist = glob('log/*.json');
$loglist = $loglist ? $loglist : array();
$loglist = array_reverse(array_values(preg_grep('/^([^.])/', $loglist)));

if ($getday == false) {
    $logs = array_slice($loglist, 0, $range);
}
$result = array();
$formatdate = substr($setUp->getConfig('time_format'), 0, 5);
?>
<form class="form-inline selectdate" method="get">
    <input type="hidden" value="go" name="log">
    <div class="form-group">
        <div class="btn-group pull-left">
            <a href="?log=go&range=1" class="btn btn-default <?php if ($range == 1) echo "active"; ?>">
                <span class="fa-stack stackalendar">
                  <i class="fa fa-calendar-o fa-stack-2x"></i>1
                </span>
            </a>
            <a href="?log=go&range=7" class="btn btn-default <?php if ($range == 7) echo "active"; ?>">
                <span class="fa-stack stackalendar">
                    <i class="fa fa-calendar-o fa-stack-2x"></i>7
                </span>
            </a>
            <a href="?log=go&range=30" class="btn btn-default <?php if ($range == 30) echo "active"; ?>">
                <span class="fa-stack stackalendar">
                  <i class="fa fa-calendar-o fa-stack-2x"></i>30
                </span>
            </a>
        </div>
    </div>
    <div class="form-group">
        <select name="day" class="form-control" onchange="this.form.submit()">
        <option><?php echo $encodeExplorer->getString("select_date"); ?></option>
        <?php
        foreach ($loglist as $item) { 
            $name = basename($item, '.json'); 
            $listtime = strtotime($name);
            $showtime = date($formatdate, $listtime);
            ?>
        <option
        <?php 
        if ($day == $name) {
            echo " selected";
        } ?> value="<?php echo $name; ?>">
        <?php echo $showtime; ?>
        </option>
        <?php
        } ?>
        </select>
    </div>
</form>
