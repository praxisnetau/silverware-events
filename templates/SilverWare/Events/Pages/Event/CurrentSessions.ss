<% if $CurrentSessions %>
  <div class="sessions current">
    <h3>Current Sessions</h3>
    <ul>
      <% loop $CurrentSessions %>
        <li class="session"><a href="$Link">$Title</a></li>
      <% end_loop %>
    </ul>
  </div>
<% end_if %>
