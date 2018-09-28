<% if $UpcomingSessions %>
  <div class="sessions upcoming">
    <h3>Upcoming Sessions</h3>
    <ul>
      <% loop $UpcomingSessions %>
        <li class="session"><a href="$Link">$Title</a></li>
      <% end_loop %>
    </ul>
  </div>
<% end_if %>
