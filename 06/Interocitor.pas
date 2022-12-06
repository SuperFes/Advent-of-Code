program Interocitor;

uses crt;

var
  SpaceData : File of Char;
  charCount, firstMarker : Integer;
  currChar : Char;
  char1, char2, char3 : Char;

begin
    if (ParamCount <> 1) then
    begin
        Writeln('Usage: Interocitor <filename.data>');
        Writeln;
        Halt;
    end;

    Assign(SpaceData, ParamStr(1));
    Reset(SpaceData);

    charCount := 0;
    firstMarker := -1;

    char1 := ' ';
    char2 := ' ';
    char3 := ' ';

    while not Eof(SpaceData) do
    begin
        Read(SpaceData, currChar);
        charCount := charCount + 1;

        if (charCount > 3) and
           (currChar <> char1) and
           (currChar <> char2) and
           (currChar <> char3) and
           (char1 <> char2) and
           (char3 <> char2) and
           (char3 <> char1)
           then
        begin
            firstMarker := charCount;
            break;
        end;

        char1 := char2;
        char2 := char3;
        char3 := currChar;
    end;

    Writeln('The first marker occurs after character: ', firstMarker);

    Close(SpaceData);
end.
