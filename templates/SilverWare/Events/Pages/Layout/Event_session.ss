<article>
  <% include Page\Header %>
  <% include Page\Image %>
  <% include SilverWare\Events\Pages\Event\DateAndTime %>
  <% include SilverWare\Events\Pages\Event\Overview %>
  <% with $EventSession %>
    <% include SilverWare\Extensions\Model\DetailFieldsExtension\Details %>
  <% end_with %>
</article>
