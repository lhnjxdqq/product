<{if NULL === $number}> class="track-day-null"<{elseif ($standard * 0.3) <= $number && ($standard * 0.5) > $number}> class="track-day-warnning"<{elseif ($standard * 0.5) <= $number}> class="track-day-danger"<{/if}>
