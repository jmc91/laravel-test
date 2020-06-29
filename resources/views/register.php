<html>

   <head>
      <title>Form Example</title>
   </head>

   <body>
      <form action = "/user/register" method = "post">
         <input type = "hidden" name = "_token" value = "<?php echo csrf_token() ?>">

         <table>
            <tr>
               <td>City</td>
               <td><input type = "text" name = "city" /></td>
            </tr>
            <tr>
               <td>State</td>
               <td><input type = "text" name = "state" /></td>
            </tr>
             <tr>
               <td>Date</td>
               <td><input type = "text" name = "date" /></td>
            </tr>
            <tr>
               <td>Title</td>
               <td><input type = "text" name = "title" /></td>
            </tr>
            <tr>
               <td>Media Link</td>
               <td><input type = "text" name = "media_link" /></td>
            </tr>
            <tr>
               <td>Tags</td>
               <td><input type = "text" name = "tags" /></td>
            </tr>
            <tr>
               <td>Description</td>
               <td><input type = "text" name = "description" /></td>
            </tr>
            <tr>
               <td colspan = "2" align = "center">
                  <input type = "submit" value = "Register" />
               </td>
            </tr>
         </table>

      </form>
   </body>
</html>