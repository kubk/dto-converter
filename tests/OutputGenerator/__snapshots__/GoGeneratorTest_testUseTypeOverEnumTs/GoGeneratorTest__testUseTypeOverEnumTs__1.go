// THE FILE WAS AUTOGENERATED USING PHP-CONVERTER. PLEASE DO NOT EDIT IT!
// THE FILE WAS AUTOGENERATED USING PHP-CONVERTER. PLEASE DO NOT EDIT IT!
// THE FILE WAS AUTOGENERATED USING PHP-CONVERTER. PLEASE DO NOT EDIT IT!

package gen

type ColorEnum int 

const(
  RED ColorEnum = 0
  GREEN ColorEnum = 1
  BLUE ColorEnum = 2
)

type RoleEnum string 

const(
  ADMIN RoleEnum = "admin"
  READER RoleEnum = "reader"
  EDITOR RoleEnum = "editor"
)

type User struct {
  Id string
  ThemeColor ColorEnum
  Role RoleEnum
}
