program InterocitorRedux;

uses crt;

var
   SpaceData              : File of Char;
   charCount, firstMarker : Integer;
   i, j                   : Integer;
   Checker                : Char;
   currChar               : Char;
   characters             : Array [0..14] of Char = (' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ');
   done                   : Boolean;

begin
   if (ParamCount <> 1) then
   begin
      Writeln('Usage: InterocitorRedux <filename.data>');
      Writeln;
      Halt;
   end;

   Assign(SpaceData, ParamStr(1));
   Reset(SpaceData);

   charCount := 13;
   firstMarker := 0;

   done := false;

   while not Eof(SpaceData) and not done do
   begin
      Read(SpaceData, currChar);

      firstMarker := firstMarker + 1;

      if (firstMarker > charCount) then
      begin
         done := true;

         for i := 0 to charCount do
         begin
            Checker := characters[i];

            for j := 0 to charCount do
            begin
               if i = j then continue;

               if Checker = characters[j] then begin
                  done := false;
               end;

               if not done then break;
            end;

            if not done then break;
         end;
      end;

      if done then break;

      for i := 0 to charCount - 1 do
      begin
         WriteLn(i);
         characters[i] := characters[i + 1];
      end;

      characters[charCount] := currChar;

      WriteLn(characters);
   end;

   if done then begin
      Writeln('The first marker occurs after character: ', firstMarker - 1);
   end
   else begin
      Writeln('The first marker was not found =(');
   end;

   Close(SpaceData);
end.
