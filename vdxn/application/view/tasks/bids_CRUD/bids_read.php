<?php
  if ($hasUserBid) {
    $curr_bid_text = "Current Bid: " . $bid->{'amount'};
    $color_bid_text = "palette palette-nephritis";
  } else {
    $curr_bid_text = "You haven't bidded yet!";
    $color_bid_text = "palette palette-midnight-blue";
  }
?>
<dl class="<?php echo $color_bid_text; ?>" style="border-radius: 8px 8px 0px 0px">
  <dt><?php echo $curr_bid_text; ?></dt>
  <small>Max bid:, Min bid:</small>
</dl>
