module class_SandyCave
  implicit none
  private

  type, public :: Point
     integer :: X, Y
     character :: Pixel = ' '
  end type Point

  type, public :: Sand
     integer :: X, Y
     logical :: Fell
     character :: Pixel = '⸰'
  end type Sand

  type, public :: Wall
     integer :: X, Y
     character :: Pixel = '🮋'
  end type Wall

  type, public :: Floor
     integer :: Y
     character :: Pixel = '🮁'
     logical :: CanFloor
  end type Floor
end module class_SandyCave

integer function ReadNextInt(Lun, IOState) result(Res)
  integer, intent(in) :: Lun
  integer, intent(inout) :: IOState
  character(4) :: String
  character(10) :: Format
  character :: Byte
  logical :: FoundNummies

  String = ''
  FoundNummies = .false.

  Reading: do
     read (Lun, fmt="(A)", advance='no', iostat=IOState) Byte

     if (IOState .ne. 0) then
        exit Reading
     end if

     if (Byte >= '0' .and. Byte <= '9') then
        FoundNummies = .true.

        String = trim(String) // trim(Byte)
     else if (FoundNummies .eqv. .false.) then
        cycle Reading
     else
        exit Reading
     end if
  end do Reading

  if (IOState == 0 .or. is_iostat_eor(IOState)) then
     read (String,*) Res
  else
     Res = 0
  end if
end function ReadNextInt

program SandyCave
  use class_SandyCave
  implicit none

  integer :: ReadNextInt

  type(Wall) :: brock
  type(Sand) :: crack
  type(Floor) :: flir
  type(Point) :: pixel
  character(100) :: fileName
  integer :: startX, startY
  integer :: endX, endY
  character :: comma
  character(4) :: arrow
  integer :: curX, curY
  integer :: cursor
  character :: matrix(1000, 500)
  integer :: tehSands
  integer :: IOState
  integer :: yEst

  if (command_argument_count() .ne. 1) then
     print *, "You must specify a file name, KTHXBAI"

     stop
  endif

  call get_command_argument(1, fileName)

  open(1, file=fileName, status='unknown', form='formatted')

  pixel = Point(0, 0, ' ')
  brock = Wall(0, 0, 'X')

  Out: do curX = 1,1000
     Re: do curY = 1,500
        matrix(curX, curY) = pixel%Pixel
     end do Re
  end do Out

  yEst = 0

  DrawWalls: do
     endX = ReadNextInt(1, IOState)
     endY = ReadNextInt(1, IOState)

     endY = endY + 1

     if (.not. IOState .eq. 0) then
        exit DrawWalls
     end if

     MakeWalls: do
        startX = endX
        startY = endY

        endX = ReadNextInt(1, IOState)
        endY = ReadNextInt(1, IOState)

        endY = endY + 1

        if (endX .eq. 0 .and. endY .eq. 0) then
           exit MakeWalls
        end if

        print *, startX, 'x', startY, ' -> ', endX, 'x', endY

        curX = startX
        curY = startY

        DryWall: do
           matrix(curX, curY) = brock%Pixel

           if (curX .eq. endX .and. curY .eq. endY) then
              exit DryWall
           else if (curX .eq. endX) then
              if (curY < endY) then
                 curY = curY + 1
              else
                 curY = curY - 1
              end if
           else if (curY .eq. endY) then
              if (curX < endX) then
                 curX = curX + 1
              else
                 curX = curX - 1
              end if
           end if
        end do DryWall

        if (curY > yEst) then
           yEst = curY
        end if

        if (is_iostat_eor(IOState)) then
           exit MakeWalls
        end if
     end do MakeWalls
  end do DrawWalls

  do curY = 1,11
     print *, matrix(490:505, curY), ""
  end do

  close(1)

  FallingFalling: do
     crack = Sand(500, 1, .false., "o")

     ThisFall: do
        if (crack%Y > 499) then
           crack%Fell = .true.
           print *, "I die..."
           exit ThisFall
        else if (matrix(crack%X, crack%Y + 1) .eq. ' ') then
           crack%Y = crack%Y + 1
        else if (matrix(crack%X - 1, crack%Y + 1) .eq. ' ') then
           crack%X = crack%X - 1
           crack%Y = crack%Y + 1
        else if (matrix(crack%X + 1, crack%Y + 1) .eq. ' ') then
           crack%X = crack%X + 1
           crack%Y = crack%Y + 1
        else
           print *, "I stop !!! ", crack%X, crack%Y

           matrix(crack%X, crack%Y) = crack%Pixel

           exit ThisFall
        end if
     end do ThisFall

     if (crack%Fell .or. crack%Y .eq. 1) then
        exit FallingFalling
     end if

     tehSands = tehSands + 1
  end do FallingFalling

  do curY = 1,11
     print *, matrix(490:505, curY), ""
  end do

  print *, "Like sand in the hourglass, there were ", tehSands, " sands"

  print *, yEst
  flir  = Floor(yEst + 2, '=', .false.)

  print *, "Fill in the INFINITE FLOOR!"

  Put: do curX = 1,1000
     matrix(curX, flir%Y) = flir%Pixel
  end do Put

  do curY = 1,11
     print *, matrix(490:505, curY), ""
  end do

  FlooringFlooring: do
     crack = Sand(500, 1, .false., "o")

     ThisFloor: do
        if (crack%Y > 499) then
           crack%Fell = .true.
           print *, "I die..."
           exit ThisFloor
        else if (matrix(crack%X, crack%Y + 1) .eq. ' ') then
           crack%Y = crack%Y + 1
        else if (matrix(crack%X - 1, crack%Y + 1) .eq. ' ') then
           crack%X = crack%X - 1
           crack%Y = crack%Y + 1
        else if (matrix(crack%X + 1, crack%Y + 1) .eq. ' ') then
           crack%X = crack%X + 1
           crack%Y = crack%Y + 1
        else
           print *, "I stop !!! ", crack%X, crack%Y

           matrix(crack%X, crack%Y) = crack%Pixel

           exit ThisFloor
        end if
     end do ThisFloor

     if (crack%Fell) then
        print *, "I fall ___ "

        exit FlooringFlooring
     end if

     tehSands = tehSands + 1

     if (crack%Y == 1) then
        print *, "I fall ___ "

        exit FlooringFlooring
     end if
  end do FlooringFlooring

  do curY = 1,yEst+1
     print *, matrix(485:515, curY), ""
  end do

  print *, "Like sand in the underpants, there were ", tehSands, " sands"
end program SandyCave
